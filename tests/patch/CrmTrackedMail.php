<?php

namespace App\Mail;

use App\Models\CrmContact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/**
 * Generic tracked mailable used by the cadence engine and bulk sends.
 * Every send gets a unique message_token, stamped into the Message-ID
 * header, so a bounce DSN can be matched back to the exact send + contact
 * without needing an ESP webhook.
 */
class CrmTrackedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $messageToken;

    public function __construct(
        public CrmContact $contact,
        public string $emailSubject,
        public string $bodyHtml,
    ) {
        $this->messageToken = (string) Str::uuid();
    }

    public function build(): static
    {
        $mail = $this->subject($this->emailSubject)->html($this->bodyHtml);
        $domain = parse_url(config('app.url'), PHP_URL_HOST) ?: 'simplytronix.com';
        $messageId = "{$this->messageToken}@{$domain}";

        // Laravel 9+ (Symfony Mailer): use withSymfonyMessage instead.
        // Check your composer.json — swap this block if you're on 9+.
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
