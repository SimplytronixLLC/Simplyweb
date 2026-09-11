#!/usr/bin/env python3
"""
Two changes, based on DigiKey's official documented limits
(https://developer.digikey.com/tutorials-and-resources/shared-concepts):
Product Information API = 120 requests/minute, 1,000 requests/day, PER KEY.

1. THROTTLE SAFETY MARGIN (1.5x slower)
   Current sleep is exactly 500ms between requests = exactly 120/min =
   100% of DigiKey's documented burst limit, with ZERO margin. Any small
   timing jitter can trigger 'BurstLimit exceeded' 429s. This raises the
   sleep to 750ms (500 * 1.5) = 80/min = a 33% safety margin below the
   real 120/min cap, in both products:sync-specs AND
   products:sync-specs-priority (they each had their own independent
   copy of this same 500ms interval).

2. RAISE THE DAILY PROCESSING LIMIT TO USE FULL KEY CAPACITY
   With reserveAvailableKey() now properly rotating across all keys
   (from the previous patch) and SAFE_DAILY_CAP=990 per key, real
   available capacity is (number of configured keys) x 990/day - but
   the scheduled run was still capped at 2,200/day, set back when far
   fewer keys existed. This updates the digikey_sync_schedule setting
   to use the real current capacity instead of the stale limit.

Run from the Laravel app root (same directory as artisan).
"""
import shutil
import sys
from datetime import datetime

TS = datetime.now().strftime('%Y%m%d-%H%M%S')

FILES = {
    'app/Console/Commands/SyncCachedProductSpecs.php': [
        ('$this->line("Rate Limit        : 1 request every 0.5 sec");',
         '$this->line("Rate Limit        : 1 request every 0.75 sec (33% margin below DigiKey\'s 120/min burst limit)");'),
        ('// ── 0.5-second rate-limit sleep ───────────────────────',
         '// ── 0.75-second rate-limit sleep (33% margin below DigiKey\'s 120/min burst limit) ───────────────────────'),
        ("if ($checked < $limit) {\n                        $this->line('Sleeping 0.5 seconds...');\n                        if ($this->interruptibleSleep(500)) {\n                            return false;\n                        }\n                    }",
         "if ($checked < $limit) {\n                        $this->line('Sleeping 0.75 seconds...');\n                        if ($this->interruptibleSleep(750)) {\n                            return false;\n                        }\n                    }"),
    ],
    'app/Console/Commands/SyncPriorityProductSpecs.php': [
        ('$this->line("Rate Limit        : 1 request every 0.5 sec");',
         '$this->line("Rate Limit        : 1 request every 0.75 sec (33% margin below DigiKey\'s 120/min burst limit)");'),
        ("if ($checked < $limit) {\n                        $this->line('Sleeping 0.5 seconds...');\n                        if ($this->interruptibleSleep(500)) {\n                            return false;\n                        }\n                    }",
         "if ($checked < $limit) {\n                        $this->line('Sleeping 0.75 seconds...');\n                        if ($this->interruptibleSleep(750)) {\n                            return false;\n                        }\n                    }"),
    ],
}


def patch_file(path, replacements):
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()

    for old, new in replacements:
        count = content.count(old)
        if count == 0:
            print(f"SKIP  {path}: a pattern was not found (file may have changed) - no changes made to this file")
            return False
        if count > 1:
            print(f"ABORT {path}: a pattern matched {count} times, expected exactly 1 - refusing to guess")
            return False
        content = content.replace(old, new, 1)

    backup = f"{path}.bak-{TS}"
    shutil.copy2(path, backup)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)

    print(f"OK    {path} patched (backup: {backup})")
    return True


def main():
    ok = True
    for path, replacements in FILES.items():
        ok = patch_file(path, replacements) and ok

    if not ok:
        print("\nOne or more files were NOT fully patched. Nothing destructive happened.")
        sys.exit(1)

    print("\nBoth sync commands now throttle at 750ms (80/min, 33% margin below")
    print("DigiKey's real 120/min limit) instead of 500ms (120/min, 0% margin).")
    print()
    print("Next steps:")
    print("  1. php -l app/Console/Commands/SyncCachedProductSpecs.php")
    print("  2. php -l app/Console/Commands/SyncPriorityProductSpecs.php")
    print("  3. Run the separate capacity-update tinker command (see chat)")
    print("     to raise the daily limit to your real full key capacity.")
    print("  4. At 80 req/min, processing the full daily capacity takes")
    print("     roughly (capacity / 80) minutes - e.g. 6,930 calls is about")
    print("     87 minutes. Make sure your cron/hosting timeout allows a")
    print("     single run to run that long, or the backlog will clear")
    print("     more slowly across multiple days instead.")


if __name__ == '__main__':
    main()
