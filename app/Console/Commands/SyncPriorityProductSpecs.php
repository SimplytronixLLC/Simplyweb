<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\CachedProduct;
use App\Services\DigiKeyService;
use Symfony\Component\Console\Command\SignalableCommandInterface;

class SyncPriorityProductSpecs extends Command implements SignalableCommandInterface
{
    protected $signature   = 'products:sync-specs-priority {limit=1800}';
    protected $description = 'Safely sync DigiKey specs for high-priority semiconductor categories';

    protected string $terminationType = 'completed';
    protected string $lastErrorCode   = 'None';
    protected bool   $interrupted     = false;

    // Top unsynced categories by volume — adjust freely
    protected array $priorityCategories = [
        'LDO Voltage Regulators',
        '8-bit Microcontrollers - MCU',
        'Switching Voltage Regulators',
        'NOR Flash',
        'ARM Microcontrollers - MCU',
        'MOSFETs',
        'EEPROM',
        '16-bit Microcontrollers - MCU',
        'Schottky Diodes & Rectifiers',
        'ESD Protection Diodes / TVS Diodes',
        'Operational Amplifiers - Op Amps',
        'Logic Gates',
        'Analog to Digital Converters - ADC',
        'Supervisory Circuits',
        'Bipolar Transistors - BJT',
    ];

    public function getSubscribedSignals(): array
    {
        return [SIGINT, SIGTERM];
    }

    public function handleSignal(int $signal): void
    {
        $this->terminationType = ($signal === SIGTERM) ? 'admin stop' : 'ctrl+c';
        $this->interrupted     = true;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function isForceStop(): bool
    {
        $val = (int) DB::table('system_settings')
            ->where('setting_key', 'digikey_priority_force_stop')
            ->value('setting_value');

        if ($val) {
            $this->terminationType = 'admin stop';
            $this->interrupted     = true;
            DB::table('system_settings')->updateOrInsert(
                ['setting_key' => 'digikey_priority_force_stop'],
                ['setting_value' => 0, 'updated_at' => now()]
            );
            return true;
        }

        return false;
    }

    private function shouldAbort(): bool
    {
        if (function_exists('pcntl_signal_dispatch')) {
            pcntl_signal_dispatch();
        }

        return $this->interrupted || $this->isForceStop();
    }

    private function interruptibleSleep(int $ms): bool
    {
        $ticks = (int) ceil($ms / 100);

        for ($i = 0; $i < $ticks; $i++) {
            if ($this->shouldAbort()) {
                return true;
            }
            usleep(100_000);
        }

        return false;
    }

    private function printKeyStatuses(DigiKeyService $service): void
    {
        $this->line('');
        $this->line('┌─────────────────────────────────────────────────────┐');
        $this->line('│  DIGIKEY KEY STATUS (PRIORITY SYNC)                 │');
        $this->line('├───────┬──────────────┬────────┬──────────┬──────────┤');
        $this->line('│ Index │  Client ID   │ Active │ 429 Cnt  │ Blocked  │');
        $this->line('├───────┼──────────────┼────────┼──────────┼──────────┤');

        foreach ($service->keyStatuses() as $k) {
            $active  = $k['active']        ? '  ✔    ' : '       ';
            $blocked = $k['blocked_until'] ? substr($k['blocked_until'], 11, 8) : '  no   ';
            $this->line(sprintf(
                '│  %3d  │ %-12s │%s│   %4d   │ %s │',
                $k['index'],
                $k['client_id'],
                $active,
                $k['consecutive_429'],
                $blocked
            ));
        }

        $this->line('└───────┴──────────────┴────────┴──────────┴──────────┘');
        $this->line('');
    }

    // ── Main ─────────────────────────────────────────────────────────────────

    public function handle(): int
    {
        $startedAt  = now();
        $finishedAt = null;

        $updated = 0;
        $checked = 0;
        $failed  = 0;
        $skipped = 0;

        DB::table('system_settings')->updateOrInsert(
            ['setting_key' => 'digikey_priority_pid'],
            ['setting_value' => getmypid(), 'updated_at' => now()]
        );
        DB::table('system_settings')->updateOrInsert(
            ['setting_key' => 'digikey_priority_running'],
            ['setting_value' => 1, 'updated_at' => now()]
        );
        DB::table('system_settings')->updateOrInsert(
            ['setting_key' => 'digikey_priority_force_stop'],
            ['setting_value' => 0, 'updated_at' => now()]
        );

        try {

            $limit   = (int) $this->argument('limit');
            $service = app(DigiKeyService::class);

            $service->debugCallback = fn($msg) => $this->line("API DEBUG      : {$msg}");

            $lastId = (int) DB::table('system_settings')
                ->where('setting_key', 'digikey_priority_last_id')
                ->value('setting_value');

            $this->info('================================================');
            $this->info('DIGIKEY PRIORITY SPECS SYNC STARTED');
            $this->info('================================================');
            $this->line("Started At        : {$startedAt}");
            $this->line("Daily Limit       : {$limit}");
            $this->line("Rate Limit        : 1 request every 0.5 sec");
            $this->line("Resume From ID >  : {$lastId}");
            $this->line("Categories        : " . count($this->priorityCategories));
            $this->info('================================================');

            $this->printKeyStatuses($service);

            CachedProduct::whereIn('category', $this->priorityCategories)
            ->where(function ($q) {
                $q->whereNull('specs')
                  ->orWhereJsonLength('specs', 0)
                  ->orWhere('specs_synced', 0)
                  ->orWhereNull('datasheet');
            })
            ->whereNotNull('product_key')
            ->where('id', '>', $lastId)
            ->orderBy('id')
            ->chunkById(5, function ($products) use (
                $service, &$updated, &$checked,
                &$failed, &$skipped, $limit
            ) {

                foreach ($products as $product) {

                    if ($this->shouldAbort()) {
                        return false;
                    }

                    if ($checked >= $limit) {
                        return false;
                    }

                    $isPaused = (int) DB::table('system_settings')
                        ->where('setting_key', 'digikey_priority_paused')
                        ->value('setting_value');

                    if ($isPaused) {
                        $this->warn('SYNC PAUSED — waiting for resume...');

                        while (true) {
                            if ($this->shouldAbort()) {
                                return false;
                            }

                            $isPaused = (int) DB::table('system_settings')
                                ->where('setting_key', 'digikey_priority_paused')
                                ->value('setting_value');

                            if (!$isPaused) {
                                $this->info('SYNC RESUMED');
                                break;
                            }

                            if ($this->interruptibleSleep(500)) {
                                return false;
                            }
                        }
                    }

                    $current = $checked + 1;

                    $this->newLine();
                    $this->line('------------------------------------------------');
                    $this->line("REQUEST         : {$current}/{$limit}");
                    $this->line("PRODUCT ID      : {$product->id}");
                    $this->line("CATEGORY        : {$product->category}");
                    $this->line("PRODUCT KEY     : {$product->product_key}");
                    $this->line("TIME            : " . now());
                    $this->line('------------------------------------------------');

                    try {

                        $start  = microtime(true);
                        $result = $service->fetchProductSpecs(
                            $product->product_key,
                            'SyncPriorityProductSpecs@handle'
                        );

                        if (isset($result['status_code']) && $result['status_code'] === 429) {
                            $this->terminationType = 'api exhausted';
                            $this->lastErrorCode   = '429';
                            $this->interrupted     = true;

                            $this->error('ALL API KEYS EXHAUSTED — stopping sync');
                            $this->printKeyStatuses($service);
                            return false;
                        }

                        $duration = round(microtime(true) - $start, 2);

                        if (!$result) {
                            $failed++;
                            $this->warn("STATUS          : NO SPECS FOUND");
                            $this->line("RESPONSE TIME   : {$duration}s");
                        } else {
                            $specs     = $result['specs']     ?? [];
                            $datasheet = $result['datasheet'] ?? null;

                            if (!empty($specs)) {
                                DB::table('cached_products')
                                    ->where('id', $product->id)
                                    ->update([
                                        'specs'        => json_encode($specs),
                                        'datasheet'    => $datasheet,
                                        'specs_synced' => 1,
                                        'updated_at'   => now(),
                                    ]);

                                $updated++;
                                $this->info("STATUS          : UPDATED");
                                $this->line("SPECS COUNT     : " . count($specs));
                                $this->line("DATASHEET       : " . ($datasheet ?? 'none'));
                                $this->line("RESPONSE TIME   : {$duration}s");
                            } else {
                                $skipped++;
                                $this->warn("STATUS          : EMPTY SPECS");
                            }
                        }

                    } catch (\Throwable $e) {

                        $failed++;
                        $this->lastErrorCode = $e->getCode() ?: $e->getMessage();

                        if (str_contains(strtolower($e->getMessage()), 'network')) {
                            $this->terminationType = 'network error';
                        }

                        Log::error('DigiKey Priority Sync Error', [
                            'id'    => $product->id,
                            'part'  => $product->product_key,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);

                        $this->error("STATUS          : ERROR — " . $e->getMessage());
                    }

                    $checked++;

                    DB::table('system_settings')->updateOrInsert(
                        ['setting_key' => 'digikey_priority_last_id'],
                        ['setting_value' => $product->id, 'updated_at' => now()]
                    );

                    DB::table('system_settings')->updateOrInsert(
                        ['setting_key' => 'digikey_priority_stats'],
                        ['setting_value' => json_encode([
                            'checked'    => $checked,
                            'updated'    => $updated,
                            'failed'     => $failed,
                            'skipped'    => $skipped,
                            'limit'      => $limit,
                            'active_key' => $service->activeIndex,
                        ]), 'updated_at' => now()]
                    );

                    $this->line("PROGRESS        : {$checked}/{$limit} | UPD:{$updated} FAIL:{$failed} SKIP:{$skipped}");
                    $this->line("LAST SAVED ID   : {$product->id}");
                    $this->line("ACTIVE KEY      : #{$service->activeIndex}");
                    $this->line('------------------------------------------------');

                    if ($checked < $limit) {
                        $this->line('Sleeping 0.5 seconds...');
                        if ($this->interruptibleSleep(500)) {
                            return false;
                        }
                    }
                }
            });

        } finally {

            $finishedAt = now();
            $runtime    = $startedAt->diffForHumans($finishedAt, true);

            DB::table('system_settings')->updateOrInsert(
                ['setting_key' => 'digikey_priority_running'],
                ['setting_value' => 0, 'updated_at' => now()]
            );
            DB::table('system_settings')->updateOrInsert(
                ['setting_key' => 'digikey_priority_paused'],
                ['setting_value' => 0, 'updated_at' => now()]
            );
            DB::table('system_settings')->updateOrInsert(
                ['setting_key' => 'digikey_priority_last_summary'],
                ['setting_value' => json_encode([
                    'started_at'       => (string) $startedAt,
                    'finished_at'      => (string) $finishedAt,
                    'termination_type' => $this->terminationType,
                    'last_error_code'  => $this->lastErrorCode,
                ]), 'updated_at' => now()]
            );

            Mail::raw(
                "Run Started: {$startedAt}\n\n" .
                "Run Finished: {$finishedAt}\n\n" .
                "Runtime: {$runtime}\n\n" .
                "Total API Calls: {$checked}\n\n" .
                "Updated: {$updated} | Failed: {$failed} | Skipped: {$skipped}\n\n" .
                "Last Error Code: {$this->lastErrorCode}\n\n" .
                "Termination Type: {$this->terminationType}",
                function ($message) {
                    $message->to('info@simplytronix.com');
                    $message->subject('DigiKey Priority Sync Summary');
                }
            );
        }

        return Command::SUCCESS;
    }
}
