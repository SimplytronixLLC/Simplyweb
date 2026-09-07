<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = App\Models\CrmContact::where('name', 'like', '%Srikanth%')
        ->orWhere('email', 'like', '%kathi%')
        ->first();

if (!$c) {
    echo "NOT FOUND\n";
    exit;
}

echo "=== Contact ===\n";
print_r($c->only(['id','name','email','stage','automation_enabled','followup_step','next_followup_at','winback_track','winback_approved']));

echo "\n=== Activities ===\n";
print_r(
    $c->activities()->orderBy('created_at')->get(['type','body','created_at'])->toArray()
);
