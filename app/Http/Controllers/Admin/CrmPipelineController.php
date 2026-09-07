<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmContact;
use Illuminate\Http\Request;
use App\Mail\CrmFollowupMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CrmPipelineController extends Controller
{
    public function index()
    {
        $stages = ['new' => 'New', 'contacted' => 'Contacted', 'quoted' => 'Quoted', 'won' => 'Won', 'lost' => 'Lost'];

        $contactsByStage = CrmContact::whereNull('duplicate_of_id')->orderByDesc('updated_at')->get()->groupBy('stage');

        return view('admin.crm.pipeline', compact('stages', 'contactsByStage'));
    }

    /**
     * Moving a card is the manual signal that stops automation for that
     * contact (see CrmContact::moveToStage()) — this is intentional, it's
     * how the "I only step in when someone replies" flow works.
     */
    public function moveStage(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:crm_contacts,id',
            'stage' => 'required|in:new,contacted,quoted,won,lost',
        ]);

        $contact = CrmContact::findOrFail($request->input('id'));
        $contact->moveToStage($request->input('stage'), 'admin');

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $contact = CrmContact::findOrFail($id);
        $activities = $contact->activities()->orderByDesc('created_at')->get();

        return response()->json([
            'contact' => $contact,
            'activities' => $activities,
        ]);
    }

    /**
     * Manual note added from the card detail panel.
     */
    public function addNote(Request $request, $id)
    {
        $request->validate(['note' => 'required|string|max:2000']);

        $contact = CrmContact::findOrFail($id);
        $contact->activities()->create([
            'type' => 'manual_note',
            'body' => $request->input('note'),
            'performed_by' => 'admin',
        ]);

        return response()->json(['success' => true]);
    }

    public function manualAction(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:none,rfq_received,quote_sent,quote_signed,quote_unsigned,no_quote,invalid_rfq',
            'note' => 'nullable|string|max:2000',
        ]);

        $contact = CrmContact::findOrFail($id);
        $contact->setManualAction($request->input('action'), $request->input('note'), 'admin');

        return response()->json(['success' => true]);
    }

    /**
     * Manual trigger for the day-0 follow-up email. This is the only way
     * automation_enabled ever gets turned on -- crm:sync-daily never starts
     * a cadence on its own, it only advances one already started here.
     */
    public function sendInitialFollowup(Request $request, $id)
    {
        $contact = CrmContact::findOrFail($id);

        if (!$contact->email) {
            return response()->json(["success" => false, "message" => "Contact has no email address."], 422);
        }

        if ($contact->automation_enabled) {
            return response()->json(["success" => false, "message" => "Follow-up cadence already running for this contact."], 422);
        }

        $cadence = config("crm.followup_cadence_days", [0, 3, 7, 14]);

        try {
            $mailable = new CrmFollowupMail($contact);
            Mail::to($contact->email)->send($mailable);
            $copy = CrmFollowupMail::$copy[0];
            $contact->logEmail("auto_email", $copy["subject"], $copy["body"], "admin", $mailable->messageToken);
        } catch (\Throwable $e) {
            Log::error("Manual day-0 follow-up failed for contact {$contact->id}: " . $e->getMessage());
            return response()->json(["success" => false, "message" => "Send failed -- check laravel log."], 500);
        }

        $contact->update([
            "automation_enabled" => true,
            "followup_step" => 1,
            "next_followup_at" => now()->addDays($cadence[1] - $cadence[0]),
        ]);

        return response()->json(["success" => true, "message" => "Day 0 email sent -- cadence is now running automatically."]);
    }
}