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

            if ($running) return;

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