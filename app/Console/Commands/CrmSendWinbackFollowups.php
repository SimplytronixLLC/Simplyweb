<?php
namespace App\Console\Commands;

use App\Mail\CrmWinbackFollowupMail;
use App\Models\CrmContact;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CrmSendWinbackFollowups extends Command
{
    protected $signature = 'crm:send-winback-followups {--confirm : Actually send emails. Without this flag, only a dry-run count is shown.}';
    protected $description = 'Send day 3/7/14 follow-up emails to winback contacts already on the automated cadence';

    // followup_step => days to add for the NEXT send after this one goes out
    protected array $nextIntervalDays = [
        0 => 4, // day 3 just sent -> next is day 7  (3 + 4 = 7)
        1 => 7, // day 7 just sent -> next is day 14 (7 + 7 = 14)
    ];

    public function handle(): int
    {
        $due = CrmContact::where('source', 'winback')
            ->where('automation_enabled', true)
            ->whereNotNull('followup_step')
            ->whereNotNull('email')
            ->where('next_followup_at', '<=', now())
            ->get();

        if (!$this->option('confirm')) {
            $this->info("DRY RUN — no emails sent.");
            $this->info("{$due->count()} contact(s) due for a follow-up.");
            foreach ($due as $c) {
                $label = ['Day 3', 'Day 7', 'Day 14'][$c->followup_step] ?? '?';
                $this->line(" - {$c->name} ({$c->email}) — {$label}");
            }
            $this->info("Re-run with --confirm to actually send.");
            return self::SUCCESS;
        }

        $sent = 0;
        $failed = 0;

        foreach ($due as $contact) {
            try {
                $step = $contact->followup_step;
                Mail::to($contact->email)->send(new CrmWinbackFollowupMail($contact, $step));

                $label = ['Day 3', 'Day 7', 'Day 14'][$step] ?? 'Follow-up';
                $contact->logEmail('winback_followup', $label, "Winback follow-up sent ({$label})");

                if ($step >= 2) {
                    // Final email sent — close out the cadence, allow re-entry into a future winback batch
                    $contact->update([
                        'stage' => 'contacted',
                        'automation_enabled' => false,
                        'followup_step' => 0,
                        'next_followup_at' => null,
                        'winback_track' => null,
                        'winback_approved' => false,
                    ]);
                } else {
                    $contact->update([
                        'followup_step' => $step + 1,
                        'next_followup_at' => now()->addDays($this->nextIntervalDays[$step]),
                    ]);
                }

                $sent++;
            } catch (\Throwable $e) {
                Log::error("Winback followup failed for contact {$contact->id}: " . $e->getMessage());
                $failed++;
            }
        }

        $this->info("Sent {$sent} follow-up email(s). {$failed} failed (check laravel log).");
        return self::SUCCESS;
    }
}
