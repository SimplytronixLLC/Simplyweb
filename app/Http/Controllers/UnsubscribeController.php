<?php

namespace App\Http\Controllers;

use App\Models\CrmContact;
use Illuminate\Http\Request;

class UnsubscribeController extends Controller
{
    /**
     * Show the confirmation page. Requires a valid Laravel signed URL —
     * link is generated per-contact via URL::signedRoute() in CrmTrackedMail,
     * so it can't be guessed or forged. Deliberately NOT a one-click action:
     * email security scanners auto-visit links in inboxes, and an instant
     * GET-triggered unsubscribe would let a scanner unsubscribe someone who
     * never clicked anything themselves.
     */
    public function show(Request $request, $contactId)
    {
        if (!$request->hasValidSignature()) {
            return $this->expiredResponse();
        }

        $contact = CrmContact::find($contactId);
        if (!$contact) {
            return $this->expiredResponse();
        }

        $alreadyUnsubscribed = !is_null($contact->unsubscribed_at);
        $confirmUrl = $request->fullUrl(); // same signed URL, reused as the POST form action

        return view('unsubscribe.confirm', compact('contact', 'alreadyUnsubscribed', 'confirmUrl'));
    }

    /**
     * Actually process the unsubscribe. Re-validates the same signature
     * (the confirm button POSTs back to the identical signed URL) so this
     * can't be triggered without the original valid link either.
     */
    public function confirm(Request $request, $contactId)
    {
        if (!$request->hasValidSignature()) {
            return $this->expiredResponse();
        }

        $contact = CrmContact::find($contactId);
        if (!$contact) {
            return $this->expiredResponse();
        }

        if (is_null($contact->unsubscribed_at)) {
            $contact->update([
                'unsubscribed_at' => now(),
                'automation_enabled' => false,
            ]);

            $contact->activities()->create([
                'type' => 'unsubscribed',
                'body' => 'Contact unsubscribed via email footer link.',
                'performed_by' => 'system',
            ]);
        }

        return view('unsubscribe.done', compact('contact'));
    }

    private function expiredResponse()
    {
        return response()->view('unsubscribe.expired', [], 200);
    }
}
