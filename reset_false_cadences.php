<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CrmContact;

$confirm = in_array('--confirm', $argv);

// Only quote/visitor sourced contacts could have been auto-started by the bug.
// Winback contacts are a separate, intentional automation flow — never touched here.
$affected = CrmContact::whereIn('source', ['quote', 'visitor'])
    ->where('automation_enabled', true)
    ->get();

echo "Found {$affected->count()} quote/visitor contact(s) currently on an automated cadence.\n\n";

foreach ($affected as $c) {
    $step = $c->followup_step;
    $label = ['Day 3', 'Day 7', 'Day 14'][$step] ?? "step {$step}";
    echo " - #{$c->id} {$c->name} <{$c->email}> — currently at {$label}, next due {$c->next_followup_at}\n";
}

if (!$confirm) {
    echo "\nDRY RUN — no changes made. Re-run with --confirm to pause these cadences.\n";
    exit;
}

foreach ($affected as $c) {
    $c->logEmail('system_note', 'Cadence paused', 'Automated follow-up cadence paused — was started by a since-fixed bug in crm:sync-daily, not a manual admin action. Use "Send Day 0 Follow-up" to restart deliberately if desired.', 'system');
    $c->update([
        'automation_enabled' => false,
        'followup_step' => 0,
        'next_followup_at' => null,
    ]);
}

echo "\nPaused {$affected->count()} contact(s). They can be manually restarted from the CRM pipeline board.\n";
