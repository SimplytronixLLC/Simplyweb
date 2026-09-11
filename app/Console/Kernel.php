<?php
namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('crm:sync-daily')
            ->dailyAt('08:00');
        $schedule->command('crm:send-winback-followups --confirm')
            ->dailyAt('08:00');

        $schedule->command('crm:process-bounces')->everyFifteenMinutes();

        $schedule->call(function () {
            $raw = DB::table('system_settings')
                ->where('setting_key', 'digikey_sync_schedule')
                ->value('setting_value');

            if (!$raw) return;

            $config        = json_decode($raw, true);
            $scheduledTime = $config['time']  ?? null;
            $limit         = $config['limit'] ?? 800;

            if (!$scheduledTime) return;

            $nowIST = now()->setTimezone('Asia/Kolkata')->format('H:i');

            if ($nowIST !== $scheduledTime) return;

            $running = (int) DB::table('system_settings')
                ->where('setting_key', 'digikey_sync_running')
                ->value('setting_value');

            if ($running) {
                $pid = (int) DB::table('system_settings')
                    ->where('setting_key', 'digikey_sync_pid')
                    ->value('setting_value');

                $pidAlive = $pid > 0 && (
                    function_exists('posix_kill')
                        ? posix_kill($pid, 0)
                        : (function () use ($pid) {
                            $output = [];
                            @exec("ps -p {$pid} -o pid=", $output);
                            return !empty($output);
                        })()
                );

                if ($pidAlive) {
                    return;
                }

                \Illuminate\Support\Facades\Log::warning(
                    "DigiKey sync lock was held for PID {$pid}, which is no longer running - ".
                    "treating as stale from a killed process and proceeding immediately."
                );
            }

            $phpBinary = '/usr/local/bin/php';
            $artisan   = base_path('artisan');
            $logFile   = storage_path('logs/digikey-sync.log');

            exec("{$phpBinary} {$artisan} products:sync-specs {$limit} >> {$logFile} 2>&1 &");

        })->everyMinute();

        $schedule->command('seo:audit')->dailyAt('03:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}