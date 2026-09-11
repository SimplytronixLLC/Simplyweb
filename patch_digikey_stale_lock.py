#!/usr/bin/env python3
"""
Make the DigiKey sync lock self-healing.

Observed live on 2026-09-09: the sync process (PID 673589) was killed from
outside PHP (no exception in laravel.log, process confirmed gone via `ps`)
almost immediately after starting (only 106/2200 done). Because the lock
is only cleared in a `finally` block, a hard kill (SIGKILL/OOM/LVE limit)
skips that entirely and leaves 'digikey_sync_running' stuck at 1 forever -
silently blocking every scheduled run after that point until someone
manually clears it. This is the same failure shape as the earlier
SyncProductsToDolibarr double-registration outage.

Fix: if the lock has been held for longer than a set timeout (default 3
hours - the real sync typically finishes well under that at current
limits), treat it as stale, log a warning, and proceed instead of
blocking forever.

Run from the Laravel app root (same directory as artisan).
"""
import shutil
import sys
from datetime import datetime

TS = datetime.now().strftime('%Y%m%d-%H%M%S')
TARGET = 'app/Console/Kernel.php'

OLD_BLOCK = """            $running = (int) DB::table('system_settings')
                ->where('setting_key', 'digikey_sync_running')
                ->value('setting_value');

            if ($running) return;"""

NEW_BLOCK = """            $lock = DB::table('system_settings')
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

def main():
    with open(TARGET, 'r', encoding='utf-8') as f:
        content = f.read()

    count = content.count(OLD_BLOCK)
    if count == 0:
        print(f"ABORT: pattern not found in {TARGET}.")
        print("File may have changed since this patch was written - nothing modified.")
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
    print("  2. This does NOT need a cache clear/restart - the scheduler")
    print("     re-reads this file each time it runs (every minute).")
    print("  3. Also run the immediate unlock (separately, right now) so")
    print("     tonight's scheduled run isn't blocked while you wait for")
    print("     the 3-hour staleness window:")
    print("       php artisan tinker --execute=\"DB::table('system_settings')->updateOrInsert(['setting_key'=>'digikey_sync_running'],['setting_value'=>0,'updated_at'=>now()]); echo 'Lock cleared.';\"")


if __name__ == '__main__':
    main()
