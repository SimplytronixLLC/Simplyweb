<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class HarvestController extends Controller
{
    public function index()
    {
        return view('admin.harvest');
    }

    public function start()
    {
        DB::table('harvest_control')->update([
            'is_running' => 1,
            'stop_requested' => 0,
        ]);

        try {
            $logFile = storage_path('logs/harvest-process.log');
            $command = 'cd ' . escapeshellarg(base_path())
                . ' && nohup /usr/local/bin/php artisan mouser:harvest >> '
                . escapeshellarg($logFile)
                . ' 2>&1 & echo $!';

            $output = shell_exec($command);
            $pid = trim($output);

            \Illuminate\Support\Facades\Log::info('Harvest start attempted (detached)', [
                'pid' => $pid,
                'command' => $command,
            ]);

            return response()->json([
                'status' => 'started',
                'pid' => $pid,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Harvest start failed', [
                'error' => $e->getMessage(),
            ]);

            DB::table('harvest_control')->update([
                'is_running' => 0,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function stop()
    {
        DB::table('harvest_control')->update([
            'stop_requested' => 1,
        ]);

        return response()->json(['status' => 'stopping']);
    }

    public function status()
    {
        $row = DB::table('harvest_control')->first();

        return response()->json($row);
    }
}
