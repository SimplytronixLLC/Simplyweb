<?php
namespace App\Mail;

use App\Models\CrmContact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CrmWinbackFollowupMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $steps = [
        0 => [
            'subject' => 'Any upcoming component needs?',
            'body' => "We wanted to check back in and see how things are progressing on your end since we last connected. If there's a project coming up where sourcing or lead times are a concern, just let us know and we're happy to help.",
        ],
        1 => [
            'subject' => 'Worth a fresh look',
            'body' => "It's been a bit since we last connected, and we didn't want to lose touch. Component pricing and availability shift quickly, so it might be worth another look at what we can offer now.",
        ],
        2 => [
            'subject' => 'Here whenever you need us',
            'body' => "We don't want to keep filling your inbox, so this will be our last note for now. If anything comes up down the line, we're just an email away.",
        ],
    ];

    public function __construct(public CrmContact $contact, public int $step) {}

    public function build()
    {
        $data = $this->steps[$this->step];

        return $this->subject($data['subject'])
            ->cc('sales@simplytronix.com')
            ->view('emails.crm.winback_followup')
            ->with([
                'contact' => $this->contact,
                'bodyText' => $data['body'],
            ]);
    }
}
