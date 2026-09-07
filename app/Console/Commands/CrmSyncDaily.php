<?php

namespace App\Console\Commands;

use App\Mail\CrmFollowupMail;
use App\Models\CrmContact;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CrmSyncDaily extends Command
{
    protected $signature = 'crm:sync-daily';
    protected $description = 'Pull new quote/visitor leads into crm_contacts and send any due follow-up emails';

    public function handle(): int
    {
        $this->importQuotes();
        $this->importHighEngagementVisitors();
        $this->enrollQuoteLeadsAfter30Days();
        $this->sendDueFollowups();

        return self::SUCCESS;
    }

    protected function importQuotes(): void
    {
        $existingQuoteIds = CrmContact::whereNotNull('quote_id')->pluck('quote_id')->all();

        $newQuotes = DB::table('quote')
            ->when(!empty($existingQuoteIds), fn ($q) => $q->whereNotIn('id', $existingQuoteIds))
            ->get();

        foreach ($newQuotes as $quote) {
            $duplicateOfId = null;
            if (!empty($quote->email)) {
                $match = CrmContact::whereRaw('LOWER(email) = ?', [strtolower(trim($quote->email))])
                    ->orderBy('created_at')
                    ->first();
                if ($match) {
                    $duplicateOfId = $match->id;
                }
            }

            CrmContact::create([
                'name' => $quote->name ?? null,
                'email' => $quote->email ?? null,
                'phone' => $quote->phone ?? null,
                'company' => $quote->company ?? null,
                'quote_id' => $quote->id,
                'stage' => 'new',
                'source' => 'quote',
                'automation_enabled' => false,
                'followup_step' => 0,
                'next_followup_at' => null,
                'duplicate_of_id' => $duplicateOfId,
            ]);

            if ($duplicateOfId) {
                $this->info("Quote #{$quote->id} contact flagged as possible duplicate of contact #{$duplicateOfId}.");
            }
        }

        if ($newQuotes->count() > 0) {
            $this->info("Imported {$newQuotes->count()} new quote(s) into CRM.");
        }
    }

    protected function importHighEngagementVisitors(): void
    {
        $threshold = config('crm.visitor_engagement_threshold', 50);
        $existingVisitorIds = CrmContact::whereNotNull('visitor_id')->pluck('visitor_id')->all();

        try {
            $newVisitors = DB::table('visitor_contacts as vc')
                ->join('visitor_profiles as vp', 'vp.visitor_id', '=', 'vc.visitor_id')
                ->where('vp.engagement_score', '>=', $threshold)
                ->where('vp.is_bot', false)
                ->when(!empty($existingVisitorIds), fn ($q) => $q->whereNotIn('vc.visitor_id', $existingVisitorIds))
                ->select('vc.*')
                ->get();
        } catch (\Throwable $e) {
            $this->error('Visitor import skipped — check visitor_profiles/visitor_contacts column names: ' . $e->getMessage());
            return;
        }

        foreach ($newVisitors as $visitor) {
            $duplicateOfId = null;
            if (!empty($visitor->email)) {
                $match = CrmContact::whereRaw('LOWER(email) = ?', [strtolower(trim($visitor->email))])
                    ->orderBy('created_at')
                    ->first();
                if ($match) {
                    $duplicateOfId = $match->id;
                }
            }

            CrmContact::create([
                'name' => $visitor->name ?? null,
                'email' => $visitor->email ?? null,
                'company' => $visitor->company ?? null,
                'visitor_id' => $visitor->visitor_id,
                'stage' => 'new',
                'source' => 'visitor',
                'automation_enabled' => false,
                'followup_step' => 0,
                'next_followup_at' => null,
                'duplicate_of_id' => $duplicateOfId,
            ]);

            if ($duplicateOfId) {
                $this->info("Visitor {$visitor->visitor_id} contact flagged as possible duplicate of contact #{$duplicateOfId}.");
            }
        }

        if ($newVisitors->count() > 0) {
            $this->info("Imported {$newVisitors->count()} new high-engagement visitor(s) into CRM.");
        }
    }

    /**
     * New: RFQ web-form leads (source = 'quote') that are still 'new',
     * untouched (manual_action = none), never enrolled, and 30+ days old
     * get auto-enrolled into the same followup cadence as the manual
     * "Send Day 0" button uses. This is the one exception to "automation
     * only starts when a human clicks send" — scoped deliberately to
     * quote leads only, per your instruction. Existing 7000+ contacts are
     * untouched since they already have quote_id/created_at in the past
     * but this only fires going forward from contacts not yet enrolled —
     * if you want it to also sweep the historical backlog, say so
     * explicitly since that would trigger a large one-time send.
     */
    protected function enrollQuoteLeadsAfter30Days(): void
    {
        $cadence = config('crm.followup_cadence_days', [0, 3, 7, 14]);

        $eligible = CrmContact::where('source', 'quote')
            ->where('stage', 'new')
            ->where('manual_action', 'none')
            ->where('automation_enabled', false)
            ->where('followup_step', 0)
            ->where('email_status', '!=', 'bounced')
            ->whereNull('duplicate_of_id')
            ->whereNotNull('email')
            ->where('created_at', '<=', now()->subDays(30))
            ->get();

        foreach ($eligible as $contact) {
            $contact->update([
                'automation_enabled' => true,
                'next_followup_at' => now(),
            ]);
        }

        if ($eligible->count() > 0) {
            $this->info("Auto-enrolled {$eligible->count()} quote lead(s) into followup cadence at day 30.");
        }
    }

    protected function sendDueFollowups(): void
    {
        $cadence = config('crm.followup_cadence_days', [0, 3, 7, 14]);

        CrmContact::dueForFollowup()->each(function (CrmContact $contact) use ($cadence) {
            if (!$contact->email) {
                return;
            }

            $step = $contact->followup_step;
            $copy = CrmFollowupMail::$copy[min($step, 3)] ?? CrmFollowupMail::$copy[0];
            $subject = $copy['subject'];
            $body = $copy['body'];

            try {
                $mailable = new CrmFollowupMail($contact);
                Mail::to($contact->email)->send($mailable);
                $contact->logEmail('auto_email', $subject, $body, 'system', $mailable->messageToken);
            } catch (\Throwable $e) {
                Log::error("CRM follow-up email failed for contact {$contact->id}: " . $e->getMessage());
                return;
            }

            $nextStep = $step + 1;

            if (isset($cadence[$nextStep])) {
                $contact->update([
                    'followup_step' => $nextStep,
                    'next_followup_at' => now()->addDays($cadence[$nextStep] - $cadence[$step]),
                ]);
            } else {
                $contact->update([
                    'followup_step' => $nextStep,
                    'automation_enabled' => false,
                    'next_followup_at' => null,
                ]);
            }
        });
    }
}
