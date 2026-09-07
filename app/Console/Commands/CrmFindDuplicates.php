<?php
namespace App\Console\Commands;

use App\Models\CrmContact;
use Illuminate\Console\Command;

class CrmFindDuplicates extends Command
{
    protected $signature = 'crm:find-duplicates {--confirm : Actually flag duplicates. Without this flag, only a dry-run report is shown.}';
    protected $description = 'Scan crm_contacts for the same email across different sources and flag them for manual review';

    public function handle(): int
    {
        $groups = CrmContact::whereNotNull('email')
            ->where('email', '!=', '')
            ->whereNull('duplicate_of_id')
            ->get()
            ->groupBy(fn ($c) => strtolower(trim($c->email)));

        $duplicateGroups = $groups->filter(fn ($g) => $g->count() > 1);

        if ($duplicateGroups->isEmpty()) {
            $this->info('No duplicate contacts found.');
            return self::SUCCESS;
        }

        $totalFlagged = 0;

        foreach ($duplicateGroups as $email => $group) {
            $sorted = $group->sortBy('created_at')->values();
            $primary = $sorted->first();
            $dupes = $sorted->slice(1);

            $this->line("Email: {$email}");
            $this->line("  Primary: #{$primary->id} ({$primary->source}, created {$primary->created_at})");

            foreach ($dupes as $dupe) {
                $this->line("  -> Duplicate: #{$dupe->id} ({$dupe->source}, created {$dupe->created_at})");
                $totalFlagged++;

                if ($this->option('confirm')) {
                    $dupe->update([
                        'duplicate_of_id' => $primary->id,
                        'duplicate_reviewed' => false,
                        'automation_enabled' => false,
                        'next_followup_at' => null,
                    ]);
                }
            }
        }

        if (!$this->option('confirm')) {
            $this->info("DRY RUN — {$totalFlagged} contact(s) would be flagged across " . $duplicateGroups->count() . " email(s).");
            $this->info('Re-run with --confirm to actually flag them.');
        } else {
            $this->info("Flagged {$totalFlagged} duplicate contact(s) across " . $duplicateGroups->count() . " email(s). Automation disabled on flagged duplicates.");
        }

        return self::SUCCESS;
    }
}
