<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        return view('admin.sync.index');
    }

    public function run(Request $request)
    {
        $limit = (int) $request->input('limit', 800);
        $limit = max(1, min(5000, $limit));

        $isRunning = (int) DB::table('system_settings')
            ->where('setting_key', 'digikey_sync_running')
            ->value('setting_value');

        if ($isRunning) {
            $pid = (int) DB::table('system_settings')
                ->where('setting_key', 'digikey_sync_pid')
                ->value('setting_value');

            if ($pid > 0 && !$this->pidIsAlive($pid)) {
                $this->resetRunningState();
            } else {
                return response()->json(['success' => false, 'message' => 'Sync already running.']);
            }
        }

        DB::table('system_settings')->updateOrInsert(
            ['setting_key' => 'digikey_specs_force_stop'],
            ['setting_value' => 0, 'updated_at' => now()]
        );
        DB::table('system_settings')->updateOrInsert(
            ['setting_key' => 'digikey_specs_paused'],
            ['setting_value' => 0, 'updated_at' => now()]
        );

        $phpBinary = PHP_BINARY;
        $artisan   = base_path('artisan');
        $logFile   = storage_path('logs/digikey-sync.log');

        exec("{$phpBinary} {$artisan} products:sync-specs {$limit} >> {$logFile} 2>&1 &");

        return response()->json(['success' => true, 'message' => 'Sync started.']);
    }

    public function pause()
    {
        $isPaused = (int) DB::table('system_settings')
            ->where('setting_key', 'digikey_specs_paused')
            ->value('setting_value');

        $newState = $isPaused ? 0 : 1;

        DB::table('system_settings')->updateOrInsert(
            ['setting_key' => 'digikey_specs_paused'],
            ['setting_value' => $newState, 'updated_at' => now()]
        );

        return response()->json([
            'success' => true,
            'paused'  => (bool) $newState,
        ]);
    }

    public function stop()
    {
        DB::table('system_settings')->updateOrInsert(
            ['setting_key' => 'digikey_specs_force_stop'],
            ['setting_value' => 1, 'updated_at' => now()]
        );

        DB::table('system_settings')->updateOrInsert(
            ['setting_key' => 'digikey_specs_paused'],
            ['setting_value' => 0, 'updated_at' => now()]
        );

        $pid = (int) DB::table('system_settings')
            ->where('setting_key', 'digikey_sync_pid')
            ->value('setting_value');

        if ($pid > 0) {
            if (function_exists('posix_kill')) {
                posix_kill($pid, SIGTERM);
            } else {
                @exec("kill -15 {$pid} 2>/dev/null");
            }
        }

        return response()->json(['success' => true, 'message' => 'Stop signal sent.']);
    }

    public function status()
    {
        $running = (int) DB::table('system_settings')
            ->where('setting_key', 'digikey_sync_running')
            ->value('setting_value');

        if ($running) {
            $pid = (int) DB::table('system_settings')
                ->where('setting_key', 'digikey_sync_pid')
                ->value('setting_value');

            if ($pid > 0 && !$this->pidIsAlive($pid)) {
                $this->resetRunningState();
                $running = 0;
            }
        }

        $paused = (int) DB::table('system_settings')
            ->where('setting_key', 'digikey_specs_paused')
            ->value('setting_value');

        $lastId = (int) DB::table('system_settings')
            ->where('setting_key', 'digikey_specs_last_id')
            ->value('setting_value');

        $statsRaw = DB::table('system_settings')
            ->where('setting_key', 'digikey_sync_stats')
            ->value('setting_value');

        $stats = $statsRaw ? json_decode($statsRaw, true) : [
            'checked' => 0, 'updated' => 0,
            'failed'  => 0, 'skipped' => 0, 'limit' => 800,
        ];

        $summaryRaw = DB::table('system_settings')
            ->where('setting_key', 'digikey_sync_last_summary')
            ->value('setting_value');

        $summary = $summaryRaw ? json_decode($summaryRaw, true) : null;

        $syncedCount = DB::table('cached_products')
            ->where('specs_synced', 1)
            ->count();

        $schedRaw = DB::table('system_settings')
            ->where('setting_key', 'digikey_sync_schedule')
            ->value('setting_value');

        $schedule = $schedRaw ? json_decode($schedRaw, true) : null;

        return response()->json([
            'running'      => (bool) $running,
            'paused'       => (bool) $paused,
            'last_id'      => $lastId,
            'stats'        => $stats,
            'summary'      => $summary,
            'synced_total' => $syncedCount,
            'schedule'     => $schedule,
        ]);
    }

    public function schedule(Request $request)
    {
        $action = $request->input('action');

        if ($action === 'clear') {
            DB::table('system_settings')->updateOrInsert(
                ['setting_key' => 'digikey_sync_schedule'],
                ['setting_value' => null, 'updated_at' => now()]
            );
            return response()->json(['success' => true, 'message' => 'Schedule cleared.']);
        }

        $time  = $request->input('time');
        $limit = (int) $request->input('limit', 800);

        if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
            return response()->json(['success' => false, 'message' => 'Invalid time format.']);
        }

        DB::table('system_settings')->updateOrInsert(
            ['setting_key' => 'digikey_sync_schedule'],
            [
                'setting_value' => json_encode([
                    'time'       => $time,
                    'limit'      => $limit,
                    'timezone'   => config('app.timezone'),
                    'created_at' => now()->toDateTimeString(),
                ]),
                'updated_at' => now(),
            ]
        );

        return response()->json(['success' => true, 'message' => "Scheduled daily at {$time}."]);
    }

    public function logTail(Request $request)
    {
        $logFile = storage_path('logs/digikey-sync.log');
    
        if (!file_exists($logFile)) {
            return response()->json(['lines' => [], 'offset' => 0]);
        }
    
        $size = filesize($logFile);
    
        // First load — return current end position so UI only sees new lines
        if ($request->query('offset') === 'end') {
            return response()->json(['lines' => [], 'offset' => $size]);
        }
    
        $offset = (int) $request->query('offset', 0);
        if ($offset > $size) $offset = 0;
        if ($offset === $size) return response()->json(['lines' => [], 'offset' => $offset]);
    
        $fp = fopen($logFile, 'rb');
        fseek($fp, $offset);
        $chunk = fread($fp, 131072);
        fclose($fp);
    
        $newOffset = $offset + strlen($chunk);
        $lines     = explode("\n", $chunk);
        if (end($lines) === '') array_pop($lines);
    
        return response()->json(['lines' => $lines, 'offset' => $newOffset]);
    }

    private function pidIsAlive(int $pid): bool
    {
        if (function_exists('posix_kill')) {
            return posix_kill($pid, 0);
        }
        $output = [];
        @exec("ps -p {$pid} -o pid=", $output);
        return !empty($output);
    }

    private function resetRunningState(): void
    {
        DB::table('system_settings')->updateOrInsert(
            ['setting_key' => 'digikey_sync_running'],
            ['setting_value' => 0, 'updated_at' => now()]
        );
        DB::table('system_settings')->updateOrInsert(
            ['setting_key' => 'digikey_specs_paused'],
            ['setting_value' => 0, 'updated_at' => now()]
        );
        DB::table('system_settings')->updateOrInsert(
            ['setting_key' => 'digikey_specs_force_stop'],
            ['setting_value' => 0, 'updated_at' => now()]
        );
    }
}