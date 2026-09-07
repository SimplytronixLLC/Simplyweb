<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class ContactImportController extends Controller
{
    /**
     * Show import form
     */
    public function index()
    {
        $contactCount = Contact::count();
        $lastImport = Contact::where('source', 'master_list')
            ->orderBy('created_at', 'desc')
            ->first();

        return view('admin.crm.import.index', [
            'contactCount' => $contactCount,
            'lastImport' => $lastImport,
        ]);
    }

    /**
     * Get contacts for bulk email (AJAX)
     */
    public function getContacts(Request $request)
    {
        $page = max(1, (int) $request->get('page', 1));
        $perPage = (int) $request->get('per_page', 50);
        $perPage = in_array($perPage, [25, 50, 100, 500, 1000], true) ? $perPage : 50;

        $search = trim((string) $request->get('search', ''));
        $source = trim((string) $request->get('source', ''));
        $status = $request->get('status', 'active'); // active | inactive | all
        $recency = trim((string) $request->get('recency', '')); // '' | days (int) | 'never'

        $query = Contact::select('id', 'email', 'name', 'source', 'email_status', 'last_contacted_at')
            ->whereNotNull('email');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($source !== '') {
            $query->where('source', $source);
        }

        if ($status === 'active') {
            $query->where(function ($q) {
                $q->where('email_status', '!=', 'bounced')->orWhereNull('email_status');
            })->whereNull('unsubscribed_at');
        } elseif ($status === 'inactive') {
            $query->where(function ($q) {
                $q->where('email_status', 'bounced')->orWhereNotNull('unsubscribed_at');
            });
        }
        // 'all' -> no status filter

        if ($recency === 'never') {
            $query->whereNull('last_contacted_at');
        } elseif ($recency !== '' && is_numeric($recency)) {
            $cutoff = now()->subDays((int) $recency);
            $query->where(function ($q) use ($cutoff) {
                $q->whereNull('last_contacted_at')->orWhere('last_contacted_at', '<', $cutoff);
            });
        }
        // '' -> no recency filter (show everyone regardless of last contact)

        $total = $query->count();

        $contacts = $query
            ->orderBy('name')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return response()->json([
            'contacts' => $contacts,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
        ]);
    }

    /**
     * Preview import data
     */
    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->path());
            $worksheet = $spreadsheet->getSheetByName('Master List');

            if (!$worksheet) {
                return response()->json([
                    'error' => 'Sheet "Master List" not found',
                ], 422);
            }

            $rows = $worksheet->toArray();
            array_shift($rows);

            $sample = array_slice($rows, 0, 5);
            $preview = [];

            foreach ($sample as $row) {
                $email = trim($row[0] ?? null);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $preview[] = [
                        'email' => $email,
                        'name' => trim($row[2] ?? null),
                        'company' => trim($row[1] ?? null),
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'total_rows' => count($rows),
                'valid_emails' => count(array_filter($rows, fn($row) => filter_var(trim($row[0] ?? null), FILTER_VALIDATE_EMAIL))),
                'preview' => $preview,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Process import
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            $file = $request->file('file');
            $skipDuplicates = $request->boolean('skip_duplicates', true);

            $spreadsheet = IOFactory::load($file->path());
            $worksheet = $spreadsheet->getSheetByName('Master List');

            if (!$worksheet) {
                return response()->json(['error' => 'Sheet not found'], 422);
            }

            $rows = $worksheet->toArray();
            array_shift($rows);

            DB::beginTransaction();

            $stats = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'invalid' => 0];
            $existingEmails = $skipDuplicates ? Contact::pluck('email')->toArray() : [];

            foreach ($rows as $row) {
                $email = trim($row[0] ?? null);
                $company = trim($row[1] ?? null);
                $name = trim($row[2] ?? null);
                $phone = trim($row[3] ?? null);

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $stats['invalid']++;
                    continue;
                }

                if ($skipDuplicates && in_array($email, $existingEmails)) {
                    $stats['skipped']++;
                    continue;
                }

                $company = $this->cleanValue($company);
                $name = $this->cleanValue($name);
                $phone = $this->cleanPhoneNumber($phone);

                $data = [
                    'email' => $email,
                    'name' => $name,
                    'company' => $company,
                    'phone' => $phone,
                    'source' => 'master_list',
                    'stage' => 'new',
                    'email_status' => 'active',
                    'metadata' => ['imported_at' => now()],
                ];

                $contact = Contact::firstOrNew(['email' => $email]);
                $isNew = !$contact->exists;
                $contact->fill($data);
                $contact->save();

                if ($isNew) $stats['created']++;
                else $stats['updated']++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Import completed',
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Get import history
     */
    public function history()
    {
        $imports = Contact::where('source', 'master_list')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json(['data' => $imports]);
    }

    private function cleanValue($value)
    {
        if (!$value) return null;
        $value = trim($value, '. ');
        if (in_array($value, ['.', '..', '...', '—', '-'])) return null;
        return strlen($value) > 1 ? $value : null;
    }

    private function cleanPhoneNumber($phone)
    {
        if (!$phone) return null;
        $phone = trim($phone);
        if (strlen($phone) < 7 || in_array($phone, ['.', '—', '-'])) return null;
        return $phone;
    }
}
