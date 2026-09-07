<?php

namespace App\Mail;

use App\Models\CrmContact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CrmWinbackCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CrmContact $contact) {}

    public function build()
    {
        return $this->subject('Checking in from Simplytronix')
            ->cc('sales@simplytronix.com')
            ->view('emails.crm.winback_customer')
            ->with(['contact' => $this->contact]);
    }
}
