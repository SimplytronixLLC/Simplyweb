#!/usr/bin/env python3
"""
Replace the 3-hour stale-lock timeout (added earlier this session) with an
immediate, precise PID-alive check - the same technique already used by
the admin panel's manual "Run Sync Now" button (SyncController::run() /
pidIsAlive()), which this cron path should have matched from the start.

WHY: on 2026-09-11, a manual admin-triggered run (limit=4500, started
03:12 IST) was hard-killed partway through, leaving digikey_sync_running
stuck at 1. The scheduled 06:00 IST cron run checked the lock, saw it was
only ~2h48m old - under the 3-hour timeout - and silently skipped its
slot waiting for a threshold that hadn't been reached yet. The ENTIRE
scheduled run for the day was lost waiting on an arbitrary timer, even
though the process had clearly been dead for hours.

FIX: instead of asking "how old is this lock", ask "is the process this
lock refers to actually still running" - using the same posix_kill($pid,
0) / `ps -p` fallback already proven in SyncController::pidIsAlive().
A dead PID is detected on the very next scheduler tick (within a minute),
not after an arbitrary multi-hour wait.

Run from the Laravel app root (same directory as artisan).
"""
import shutil
import sys
from datetime import datetime

TS = datetime.now().strftime('%Y%m%d-%H%M%S')
TARGET = 'app/Console/Kernel.php'

# This matches what the lock check looks like AFTER the earlier
# patch_digikey_stale_lock.py was applied (3-hour timeout version).
OLD_BLOCK = """            $lock = DB::table('system_settings')
                ->where('setting_key', 'digikey_sync_running')
                ->first();

            $running = (int) ($lock->setting_value ?? 0);

            // A lock older than this is treated as stale (left behind by a
            // hard-killed process whose `finally` block never ran) rather
            // than blocking every future run forever.
            $staleAfterHours = 3;

            if ($running) {
                $lockAgeHours = $lock->updated_at
                    ? now()->diffInHours(\\Carbon\\Carbon::parse($lock->updated_at))
                    : 0;

                if ($lockAgeHours < $staleAfterHours) {
                    return;
                }

                \\Illuminate\\Support\\Facades\\Log::warning(
                    "DigiKey sync lock was stuck for {$lockAgeHours}h (since {$lock->updated_at}) - ".
                    "treating as stale from a killed process and proceeding."
                );
            }"""

NEW_BLOCK = """            $running = (int) DB::table('system_settings')
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

                \\Illuminate\\Support\\Facades\\Log::warning(
                    "DigiKey sync lock was held for PID {$pid}, which is no longer running - ".
                    "treating as stale from a killed process and proceeding immediately."
                );
            }"""


def main():
    with open(TARGET, 'r', encoding='utf-8') as f:
        content = f.read()

    count = content.count(OLD_BLOCK)
    if count == 0:
        print(f"ABORT: expected block (the 3-hour-timeout version) not found in {TARGET}.")
        print("If you haven't applied patch_digikey_stale_lock.py yet, apply that first.")
        print("If the file has changed some other way, paste its current contents for a fresh patch.")
        print("Nothing has been modified.")
        sys.exit(1)
    if count > 1:
        print(f"ABORT: pattern matched {count} times, expected exactly 1.")
        sys.exit(1)

    content = content.replace(OLD_BLOCK, NEW_BLOCK, 1)

    backup = f"{TARGET}.bak-{TS}"
    shutil.copy2(TARGET, backup)
    with open(TARGET, 'w', encoding='utf-8') as f:
        f.write(content)

    print(f"OK    {TARGET} patched (backup: {backup})")
    print()
    print("Next steps:")
    print("  1. php -l app/Console/Kernel.php")
    print("  2. No cache clear/restart needed - the scheduler re-reads this")
    print("     file every time it runs (every minute).")
    print("  3. If the lock is CURRENTLY stuck (check with the same tinker")
    print("     diagnostic as before), you don't need to wait for the next")
    print("     scheduled minute-tick to see this take effect for real use -")
    print("     but the fix itself only runs inside that scheduled closure,")
    print("     so the very next minute's tick will now correctly detect a")
    print("     dead PID immediately rather than waiting on a timer.")


if __name__ == '__main__':
    main()
