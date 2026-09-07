<?php
namespace App\Mail;
use App\Models\CrmContact;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CrmFollowupMail extends Mailable
{
    use Queueable, SerializesModels;
    public static array $copy = [
        0 => [
            'subject' => 'Following up on your recent RFQ',
            'body' => "It's been a little while since we received your last RFQ, and we wanted to follow up personally to see if you've had any recent shortages or new requirements come up. If you have any part numbers, quantities, or target lead times you'd like us to prioritize, just reply here and we'll get moving right away.",
        ],
        1 => [
            'subject' => 'Checking in on your sourcing needs',
            'body' => "We wanted to check back in and see how things are progressing on your end since your last RFQ. If pricing, quantities, or timelines have shifted at all, we're happy to put together an updated quote whenever it's useful.",
        ],
        2 => [
            'subject' => "Still sourcing? We're happy to help",
            'body' => "It's been a bit since we last connected, and we didn't want your request to slip through the cracks. Component pricing and availability shift quickly, so if anything's changed on your end, let us know and we'll get you current numbers.",
        ],
        3 => [
            'subject' => 'One last note on your RFQ',
            'body' => "We don't want to keep filling your inbox, so this will be our last check-in for now. If the timing isn't right, no worries at all, we're just an email away whenever you're ready to move forward.",
        ],
    ];
    public CrmContact $contact;
    public int $step;
    public ?string $displayName;
    public ?Post $latestPost;
    public string $messageToken;

    public function __construct(CrmContact $contact)
    {
        $this->contact = $contact;
        $this->step = min($contact->followup_step, 3);
        $this->displayName = $contact->name ? ucwords(strtolower($contact->name)) : null;
        $this->latestPost = Post::where('is_published', 1)->latest()->first();
        $this->messageToken = (string) Str::uuid();
    }

    public function build()
    {
        $data = self::$copy[$this->step] ?? self::$copy[0];
        $domain = parse_url(config('app.url'), PHP_URL_HOST) ?: 'simplytronix.com';
        $messageId = "{$this->messageToken}@{$domain}";

        $mail = $this->subject($data['subject'])
            ->cc('sales@simplytronix.com')
            ->view('emails.crm.followup')
            ->with([
                'contact' => $this->contact,
                'displayName' => $this->displayName,
                'bodyText' => $data['body'],
                'latestPost' => $this->latestPost,
            ]);

        if (method_exists($mail, 'withSymfonyMessage')) {
            return $mail->withSymfonyMessage(function ($message) use ($messageId) {
                $message->getHeaders()->addIdHeader('Message-ID', $messageId);
            });
        }

        return $mail->withSwiftMessage(function ($message) use ($messageId) {
            $message->getHeaders()->addIdHeader('Message-ID', $messageId);
        });
    }
}
