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
     * Preview import data (dry run)
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
                    'error' => 'Sheet "Master List" not found in workbook',
                ], 422);
            }

            $rows = $worksheet->toArray();
            array_shift($rows); // Remove header

            // Sample 5 rows to show preview
            $sample = array_slice($rows, 0, 5);
            $preview = [];

            foreach ($sample as $row) {
                $email = trim($row[0] ?? null);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $preview[] = [
                        'email' => $email,
                        'name' => trim($row[2] ?? null),
                        'company' => trim($row[1] ?? null),
                        'phone' => trim($row[3] ?? null),
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
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Process import
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
            'skip_duplicates' => 'boolean',
            'skip_invalid' => 'boolean',
        ]);

        try {
            $file = $request->file('file');
            $skipDuplicates = $request->boolean('skip_duplicates', true);
            $skipInvalid = $request->boolean('skip_invalid', true);

            $spreadsheet = IOFactory::load($file->path());
            $worksheet = $spreadsheet->getSheetByName('Master List');

            if (!$worksheet) {
                return response()->json([
                    'error' => 'Sheet "Master List" not found',
                ], 422);
            }

            $rows = $worksheet->toArray();
            array_shift($rows); // Remove header

            DB::beginTransaction();

            $stats = [
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
                'invalid' => 0,
            ];

            $existingEmails = $skipDuplicates ? Contact::pluck('email')->toArray() : [];

            foreach ($rows as $row) {
                $email = trim($row[0] ?? null);
                $company = trim($row[1] ?? null);
                $name = trim($row[2] ?? null);
                $phone = trim($row[3] ?? null);
                $lastDateSeen = $row[6] ?? null;
                $timesSeen = $row[7] ?? null;
                $sourceFile = trim($row[8] ?? null);
                $sourceSheet = trim($row[9] ?? null);

                // Validate email
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $stats['invalid']++;
                    continue;
                }

                // Check for duplicates
                if ($skipDuplicates && in_array($email, $existingEmails)) {
                    $stats['skipped']++;
                    continue;
                }

                // Clean values
                $company = $this->cleanValue($company);
                $name = $this->cleanValue($name);
                $phone = $this->cleanPhoneNumber($phone);

                // Parse date
                $lastSeen = null;
                if ($lastDateSeen && is_string($lastDateSeen)) {
                    try {
                        $lastSeen = Carbon::createFromFormat('Y-m-d H:i:s', $lastDateSeen);
                    } catch (\Exception $e) {
                        try {
                            $lastSeen = Carbon::parse($lastDateSeen);
                        } catch (\Exception $e2) {
                            $lastSeen = null;
                        }
                    }
                }

                $data = [
                    'email' => $email,
                    'name' => $name,
                    'company' => $company,
                    'phone' => $phone,
                    'source' => 'master_list',
                    'stage' => 'lead',
                    'last_contact_date' => $lastSeen,
                    'metadata' => [
                        'times_seen' => $timesSeen,
                        'source_file' => $sourceFile,
                        'source_sheet' => $sourceSheet,
                    ],
                ];

                // Upsert contact
                $contact = Contact::firstOrNew(['email' => $email]);
                $isNew = !$contact->exists;

                $contact->fill($data);
                $contact->save();

                if ($isNew) {
                    $stats['created']++;
                } else {
                    $stats['updated']++;
                }
            }

            DB::commit();

            // Log the import
            activity('contact_import')
                ->withProperties([
                    'file_name' => $file->getClientOriginalName(),
                    'stats' => $stats,
                    'skip_duplicates' => $skipDuplicates,
                ])
                ->log('Imported master contact list');

            return response()->json([
                'success' => true,
                'message' => 'Import completed successfully',
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Download import template
     */
    public function template()
    {
        $filePath = storage_path('templates/contact-import-template.xlsx');

        if (!file_exists($filePath)) {
            abort(404, 'Template not found');
        }

        return response()->download($filePath, 'contact-import-template.xlsx');
    }

    /**
     * Get import history
     */
    public function history()
    {
        $imports = activity('contact_import')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'data' => $imports,
        ]);
    }

    /**
     * Clean company/name values
     */
    private function cleanValue($value)
    {
        if (!$value) return null;

        $value = trim($value, '. ');

        if (in_array($value, ['.', '..', '...', '—', '-'])) {
            return null;
        }

        return strlen($value) > 1 ? $value : null;
    }

    /**
     * Clean phone number
     */
    private function cleanPhoneNumber($phone)
    {
        if (!$phone) return null;

        $phone = trim($phone);

        if (strlen($phone) < 7 || in_array($phone, ['.', '—', '-'])) {
            return null;
        }

        return $phone;
    }
}
