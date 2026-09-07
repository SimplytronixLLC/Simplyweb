<?php

namespace App\Services;

use App\Mail\CrmTrackedMail;
use App\Models\CrmContact;
use App\Models\CrmEmailLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Single engine driving every automated cadence:
 *   - 'marketing' : 0/7/14-day sequence for any active lead (default track)
 *   - 'winback'   : existing one-time Dolibarr-sourced sequence
 *
 * Called from crm:sync-daily. Replaces the winback-only logic that used to
 * live directly in that command — same followup_step/next_followup_at
 * columns, now generalized by cadence_type so they don't collide across
 * tracks.
 *
 * Stop conditions (checked before every send): stage moved manually,
 * manual_action != 'none', email_status == 'bounced', automation_enabled == false.
 */
class CrmCadenceService
{
    public function runDaily(): array
    {
        $sent = 0;
        $skipped = 0;

        $this->enrollEligibleWebLeads();

        $due = CrmContact::where('automation_enabled', true)
            ->whereNotNull('cadence_type')
            ->whereNotNull('next_followup_at')
            ->where('next_followup_at', '<=', now())
            ->where('email_status', '!=', 'bounced')
            ->where('manual_action', 'none')
            ->whereNull('duplicate_of_id')
            ->get();

        foreach ($due as $contact) {
            try {
                $this->sendStep($contact);
                $sent++;
            } catch (\Throwable $e) {
                Log::error("CrmCadenceService: send failed for contact {$contact->id}: " . $e->getMessage());
                $skipped++;
            }
        }

        return ['sent' => $sent, 'skipped' => $skipped, 'checked' => $due->count()];
    }

    /**
     * Web-form leads with no manual action and still in 'new' stage 30 days
     * after creation get auto-enrolled into the marketing cadence. This is
     * the one exception to "automation only starts when a human clicks send"
     * — see PLAN.md for why.
     */
    private function enrollEligibleWebLeads(): void
    {
        $cadence = config('crm.cadence_days.marketing', [0, 7, 14]);

        CrmContact::where('lead_source', 'web_form')
            ->where('stage', 'new')
            ->where('manual_action', 'none')
            ->whereNull('cadence_type')
            ->whereNull('duplicate_of_id')
            ->where('created_at', '<=', now()->subDays(30))
            ->whereNotNull('email')
            ->chunkById(100, function ($contacts) use ($cadence) {
                foreach ($contacts as $contact) {
                    $contact->update([
                        'cadence_type' => 'marketing',
                        'automation_enabled' => true,
                        'followup_step' => 0,
                        'next_followup_at' => now(), // picked up by the same run below
                    ]);
                }
            });
    }

    private function sendStep(CrmContact $contact): void
    {
        $cadence = config("crm.cadence_days.{$contact->cadence_type}", [0, 7, 14]);
        $step = $contact->followup_step ?? 0;
        $template = $this->templateFor($contact->cadence_type, $step);

        $mailable = new CrmTrackedMail($contact, $template['subject'], $template['body']);
        Mail::to($contact->email)->send($mailable);

        CrmEmailLog::create([
            'contact_id' => $contact->id,
            'type' => 'auto_email',
            'subject' => $template['subject'],
            'body' => $template['body'],
            'performed_by' => 'system',
            'message_token' => $mailable->messageToken,
        ]);

        $nextStep = $step + 1;

        if (isset($cadence[$nextStep])) {
            $daysUntilNext = $cadence[$nextStep] - $cadence[$step];
            $contact->update([
                'followup_step' => $nextStep,
                'next_followup_at' => now()->addDays($daysUntilNext),
            ]);
        } else {
            // Cadence complete
            $contact->update([
                'followup_step' => null,
                'next_followup_at' => null,
                'automation_enabled' => false,
            ]);
        }
    }

    /**
     * Swap this for your real template store (DB-backed or Blade views) —
     * placeholder so the engine is runnable as-is.
     */
    private function templateFor(string $cadenceType, int $step): array
    {
        $key = "crm.templates.{$cadenceType}.{$step}";
        return config($key, [
            'subject' => 'Following up',
            'body' => 'Placeholder body — configure config/crm.php templates.' . $key,
        ]);
    }
}
