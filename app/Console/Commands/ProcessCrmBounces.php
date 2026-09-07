<?php

namespace App\Console\Commands;

use App\Models\CrmContact;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Polls a dedicated bounce mailbox over IMAP for DSN messages, matches them
 * back to the original send via the message_token stamped on the
 * crm_activities row by CrmTrackedMail, and updates the contact's bounce state.
 *
 * Requires the php-imap extension. Add to app/Console/Kernel.php:
 *   $schedule->command('crm:process-bounces')->everyFifteenMinutes();
 */
class ProcessCrmBounces extends Command
{
    protected $signature = 'crm:process-bounces';
    protected $description = 'Poll the bounce mailbox for DSNs and mark contacts bounced/inactive';

    public function handle(): int
    {
        if (!extension_loaded('imap')) {
            $this->error('php-imap extension is not installed.');
            return self::FAILURE;
        }

        $host = env('CRM_BOUNCE_IMAP_HOST');
        $port = env('CRM_BOUNCE_IMAP_PORT', 993);
        $user = env('CRM_BOUNCE_IMAP_USER');
        $pass = env('CRM_BOUNCE_IMAP_PASS');
        $threshold = (int) env('CRM_BOUNCE_HARD_THRESHOLD', 2);

        $mailbox = "{{$host}:{$port}/imap/ssl}INBOX";
        $conn = @imap_open($mailbox, $user, $pass);

        if (!$conn) {
            $this->error('IMAP connection failed: ' . imap_last_error());
            return self::FAILURE;
        }

        $unreadIds = imap_search($conn, 'UNSEEN') ?: [];
        $matched = 0;
        $unmatched = 0;

        foreach ($unreadIds as $msgId) {
            $structure = imap_fetchstructure($conn, $msgId);
            $headerRaw = imap_fetchheader($conn, $msgId);

            if (!$this->looksLikeDsn($structure, $headerRaw)) {
                continue;
            }

            $rawBody = imap_fetchbody($conn, $msgId, '', FT_PEEK);
            $token = $this->extractMessageToken($rawBody);
            $isHardBounce = $this->isHardBounce($rawBody);

            if ($token) {
                $activity = DB::table('crm_activities')->where('message_token', $token)->first();
                if ($activity) {
                    $this->registerBounce($activity->crm_contact_id, $isHardBounce, $threshold);
                    $matched++;
                } else {
                    $unmatched++;
                    Log::warning("crm:process-bounces — token {$token} not found in crm_activities");
                }
            } else {
                $unmatched++;
            }

            imap_setflag_full($conn, (string) $msgId, '\\Seen');
        }

        imap_close($conn);

        $this->info("Processed bounce mailbox: {$matched} matched, {$unmatched} unmatched.");
        return self::SUCCESS;
    }

    private function looksLikeDsn($structure, $headerRaw = ''): bool
    {
        // Strict RFC 3464 check: multipart/report; report-type=delivery-status
        if (isset($structure->type) && $structure->type === 1) {
            foreach ($structure->parameters ?? [] as $param) {
                if (strtolower($param->attribute) === 'report-type'
                    && strtolower($param->value) === 'delivery-status') {
                    return true;
                }
            }
        }

        // Fallback: many real-world NDRs (Exchange/Outlook especially) don't follow
        // RFC 3464 strictly. Fall back to Subject/From heuristics on the raw header.
        if ($headerRaw !== '') {
            if (preg_match('/^From:.*(postmaster|mailer-daemon)/im', $headerRaw)) {
                return true;
            }
            if (preg_match('/^Subject:.*(undeliverable|undelivered|delivery status notification|delivery failure|failure notice|returned mail|mail delivery failed|ndr)/im', $headerRaw)) {
                return true;
            }
        }

        return false;
    }

    private function extractMessageToken(string $rawBody): ?string
    {
        if (preg_match('/(?:Original-)?Message-ID:\s*<?([a-f0-9\-]{36})@[^\s>]+>?/i', $rawBody, $m)) {
            return $m[1];
        }
        return null;
    }

    private function isHardBounce(string $rawBody): bool
    {
        if (preg_match('/Action:\s*(failed|delayed)/i', $rawBody, $m)) {
            return strtolower($m[1]) === 'failed';
        }
        return (bool) preg_match('/(mailbox unavailable|user unknown|does not exist|550)/i', $rawBody);
    }

    private function registerBounce(int $contactId, bool $isHard, int $threshold): void
    {
        $contact = CrmContact::find($contactId);
        if (!$contact) {
            return;
        }

        if (!$isHard) {
            $contact->activities()->create([
                'type' => 'soft_bounce',
                'body' => 'Transient delivery failure recorded.',
                'performed_by' => 'system',
            ]);
            return;
        }

        $contact->increment('bounce_count');
        $contact->update(['last_bounced_at' => now()]);

        if ($contact->bounce_count >= $threshold) {
            $contact->update(['email_status' => 'bounced', 'automation_enabled' => false]);
            $contact->activities()->create([
                'type' => 'hard_bounce',
                'body' => "Marked inactive after {$contact->bounce_count} hard bounce(s). Automation halted.",
                'performed_by' => 'system',
            ]);
        } else {
            $contact->activities()->create([
                'type' => 'hard_bounce',
                'body' => "Hard bounce #{$contact->bounce_count} recorded.",
                'performed_by' => 'system',
            ]);
        }
    }
}
