<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Mail\CrmTrackedMail;
use App\Models\CrmContact;
use App\Models\CrmEmailBatch;
use App\Models\CrmEmailBatchRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
class CrmBulkEmailController extends Controller
{
    const SIGNATURE_KEY = 'crm_email_signature';
    public function index()
    {
        $contacts = CrmContact::whereNull('duplicate_of_id')
            ->where('email_status', '!=', 'bounced')
            ->whereNull('unsubscribed_at')
            ->whereNotNull('email')
            ->orderBy('name')
            ->get();
        $totalContacts = $contacts->count();
        $masterListCount = CrmContact::where('source', 'master_list')->whereNotNull('email')->count();
        $signature = DB::table('system_settings')
            ->where('setting_key', self::SIGNATURE_KEY)
            ->value('setting_value') ?? '';
        return view('admin.crm.bulk_email', compact('totalContacts', 'masterListCount', 'signature'));
    }
    /**
     * NOTE: sends synchronously in the request. Fine up to a few hundred
     * contacts on shared hosting; beyond that, dispatch a queued job per
     * recipient instead. Flag if your lists regularly run into the
     * thousands and I'll build the queued version.
     */
    public function send(Request $request)
    {
        $request->validate([
            'contact_ids' => 'required|array|min:1',
            'contact_ids.*' => 'integer|exists:crm_contacts,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);
        $contacts = CrmContact::whereIn('id', $request->input('contact_ids'))
            ->where('email_status', '!=', 'bounced')
            ->whereNull('unsubscribed_at')
            ->whereNotNull('email')
            ->get();
        $batch = CrmEmailBatch::create([
            'subject' => $request->input('subject'),
            'body' => $request->input('body'),
            'created_by' => $request->user()->name ?? 'admin',
            'recipient_count' => $contacts->count(),
        ]);
        foreach ($contacts as $contact) {
            $recipient = CrmEmailBatchRecipient::create([
                'batch_id' => $batch->id,
                'contact_id' => $contact->id,
                'status' => 'queued',
            ]);
            try {
                $mailable = new CrmTrackedMail($contact, $batch->subject, $batch->body);
                Mail::to($contact->email)->queue($mailable);
                $contact->activities()->create([
                    'type' => 'bulk_email',
                    'subject' => $batch->subject,
                    'body' => $batch->body,
                    'performed_by' => $batch->created_by,
                    'message_token' => $mailable->messageToken,
                ]);
                $recipient->update(['status' => 'sent', 'sent_at' => now()]);
                $contact->update(['last_contacted_at' => now()]);

                // Move fresh leads into "contacted" once they've actually been emailed.
                // Only advances from 'new' — never downgrades a contact already further
                // along the pipeline (quoted/won/lost).
                if ($contact->stage === 'new') {
                    $contact->update(['stage' => 'contacted']);
                }
            } catch (\Throwable $e) {
                Log::error("Bulk email send failed for contact {$contact->id}: " . $e->getMessage());
                $recipient->update(['status' => 'skipped']);
            }
        }
        $batch->update(['sent_at' => now()]);
        return redirect()->route('admin.crm.bulk_email')
            ->with('status', "Sent to {$contacts->count()} contact(s).");
    }
    /**
     * Save/update the reusable email signature. Stored as raw HTML in
     * system_settings — same key/value table your DigiKey sync schedule
     * already lives in.
     */
    public function saveSignature(Request $request)
    {
        $request->validate(['signature' => 'nullable|string']);
        DB::table('system_settings')->updateOrInsert(
            ['setting_key' => self::SIGNATURE_KEY],
            ['setting_value' => $request->input('signature', ''), 'updated_at' => now(), 'created_at' => now()]
        );
        return response()->json(['success' => true]);
    }
}
