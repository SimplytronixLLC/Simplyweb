<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CrmTrackedMail;
use App\Models\CrmContact;
use App\Models\CrmEmailBatch;
use App\Models\CrmEmailBatchRecipient;
use App\Models\CrmEmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CrmBulkEmailController extends Controller
{
    /**
     * Contact picker view — all contacts, checkboxes, filters by stage/
     * source/track. Reuses the same table pattern as the winback view.
     */
    public function index()
    {
        $contacts = CrmContact::whereNull('duplicate_of_id')
            ->where('email_status', '!=', 'bounced')
            ->orderBy('name')
            ->get();

        return view('admin.crm.bulk_email', compact('contacts'));
    }

    /**
     * NOTE: sends synchronously in the request. Fine up to a few hundred
     * contacts on shared hosting; beyond that, dispatch a queued job per
     * recipient instead (same body, just wrap the try/catch in a Job class
     * and push it onto the queue here). Flag if your lists regularly run
     * into the thousands and I'll build the queued version.
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
                Mail::to($contact->email)->send($mailable);

                CrmEmailLog::create([
                    'contact_id' => $contact->id,
                    'type' => 'bulk_email',
                    'subject' => $batch->subject,
                    'body' => $batch->body,
                    'performed_by' => $batch->created_by,
                    'message_token' => $mailable->messageToken,
                ]);

                $recipient->update(['status' => 'sent', 'sent_at' => now()]);
            } catch (\Throwable $e) {
                Log::error("Bulk email send failed for contact {$contact->id}: " . $e->getMessage());
                $recipient->update(['status' => 'skipped']);
            }
        }

        $batch->update(['sent_at' => now()]);

        return redirect()->route('admin.crm.bulk_email')
            ->with('status', "Sent to {$contacts->count()} contact(s).");
    }
}
