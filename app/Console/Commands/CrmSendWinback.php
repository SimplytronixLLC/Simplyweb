<?php

namespace App\Console\Commands;

use App\Mail\CrmWinbackCustomerMail;
use App\Mail\CrmWinbackProspectMail;
use App\Models\CrmContact;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CrmSendWinback extends Command
{
    protected $signature = 'crm:send-winback {--confirm : Actually send emails. Without this flag, only a dry-run count is shown.}';
    protected $description = 'Send winback emails to approved contacts (manual run only — not scheduled)';

    public function handle(): int
    {
        $pending = CrmContact::where('source', 'winback')
            ->where('winback_approved', true)
            ->whereNotNull('email')
            ->get();

        $customerCount = $pending->where('winback_track', 'customer')->count();
        $prospectCount = $pending->where('winback_track', 'prospect')->count();

        if (!$this->option('confirm')) {
            $this->info("DRY RUN — no emails sent.");
            $this->info("{$customerCount} customer-track email(s) and {$prospectCount} prospect-track email(s) would be sent.");
            $this->info("Re-run with --confirm to actually send.");
            return self::SUCCESS;
        }

        $sent = 0;
        $failed = 0;

        foreach ($pending as $contact) {
            try {
                if ($contact->winback_track === 'customer') {
                    Mail::to($contact->email)->send(new CrmWinbackCustomerMail($contact));
                    $subject = 'Checking in from Simplytronix';
                } else {
                    Mail::to($contact->email)->send(new CrmWinbackProspectMail($contact));
                    $subject = 'Still sourcing this part?';
                }

                $contact->logEmail('winback_email', $subject, "Winback email sent ({$contact->winback_track} track)");

                // Prevent double-send on accidental re-run
                $contact->update([
                    'winback_approved' => false,
                    'automation_enabled' => true,
                    'followup_step' => 0,
                    'next_followup_at' => now()->addDays(3),
                ]);

                $sent++;
            } catch (\Throwable $e) {
                Log::error("Winback email failed for contact {$contact->id}: " . $e->getMessage());
                $failed++;
            }
        }

        $this->info("Sent {$sent} winback email(s). {$failed} failed (check laravel log).");

        return self::SUCCESS;
    }
}
