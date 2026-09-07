<?php

namespace App\Mail;

use App\Models\CrmContact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

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
        $unsubscribeUrl = URL::signedRoute('crm.unsubscribe.show', ['contact' => $this->contact->id]);

        $footer = '<div style="margin-top:24px; padding-top:16px; border-top:1px solid #e5e7eb; '
            . 'font-family:Inter,system-ui,sans-serif; font-size:11.5px; color:#94a3b8;">'
            . 'You\'re receiving this because you contacted Simplytronix or requested a quote. '
            . '<a href="' . $unsubscribeUrl . '" style="color:#94a3b8; text-decoration:underline;">Unsubscribe</a>'
            . '</div>';

        $mail = $this->subject($this->emailSubject)->html($this->bodyHtml . $footer);
        $domain = parse_url(config('app.url'), PHP_URL_HOST) ?: 'simplytronix.com';
        $messageId = "{$this->messageToken}@{$domain}";

        // Laravel 9+ (Symfony Mailer): use withSymfonyMessage instead.
        // Check your composer.json — swap this block if you're on 9+.
        if (method_exists($mail, 'withSymfonyMessage')) {
            return $mail->withSymfonyMessage(function ($message) use ($messageId) {
                $message->getHeaders()->addIdHeader('Message-ID', $messageId);
            });
        }

        $bounceMailbox = env('CRM_BOUNCE_IMAP_USER', 'bounces@simplytronix.com');

        return $mail->withSwiftMessage(function ($message) use ($messageId, $bounceMailbox, $unsubscribeUrl) {
            $headers = $message->getHeaders();
            if ($headers->has('Message-ID')) {
                $headers->removeAll('Message-ID');
            }
            $headers->addIdHeader('Message-ID', $messageId);

            // Route bounce DSNs to the monitored bounce mailbox (envelope sender),
            // while the visible From: header stays info@simplytronix.com for recipients.
            $message->setReturnPath($bounceMailbox);

            // Standard unsubscribe header — mail providers use this as a trust signal
            // and some show a native "Unsubscribe" link next to the sender name.
            $headers->addTextHeader('List-Unsubscribe', '<' . $unsubscribeUrl . '>');
        });
    }
}
