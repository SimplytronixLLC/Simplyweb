<?php

namespace App\Mail;

use App\Models\CrmContact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CrmWinbackProspectMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CrmContact $contact) {}

    public function build()
    {
        return $this->subject('Still sourcing this part?')
            ->cc('sales@simplytronix.com')
            ->view('emails.crm.winback_prospect')
            ->with(['contact' => $this->contact]);
    }
}
