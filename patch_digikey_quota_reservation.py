#!/usr/bin/env python3
"""
Add proactive, cross-command-coordinated DigiKey key selection.

PROBLEM (confirmed live on 2026-09-09):
  - Key rotation was entirely REACTIVE: it only rotated after DigiKey
    itself returned a 429, meaning every key overshoots its real daily
    cap before the system reacts.
  - products:sync-specs and products:sync-specs-priority use SEPARATE
    locks (digikey_sync_running vs digikey_priority_running), so they
    can run at the same time, both reading/writing the same shared
    'digikey_active_key_index' cache value with no coordination between
    them - a genuine race condition.
  - Observed result: key 4 hit 1,562/1,000 calls (56% over, wasting 79
    calls on requests that just got rejected) while keys 1-3 sat
    completely idle and key 5 landed suspiciously exactly at 1,000.

FIX:
  Every real API call now proactively reserves a key slot via
  reserveAvailableKey(), instead of just trusting whatever was cached
  from last time:
    1. Acquires Cache::lock('digikey_key_reservation', 10) - this uses
       real filesystem flock() under the 'file' cache driver, so it's a
       genuine cross-process mutex even without Redis/database cache.
    2. Queries ACTUAL today's usage per key from the ApiUsage table
       (UTC day boundary, matching DigiKey's real reset time).
    3. Picks the lowest-usage key that's under a safe cap (990 - a
       10-call buffer below DigiKey's real 1,000 limit, to absorb the
       small race window between reservation and the ApiUsage row
       actually being written) and not currently 429-blocked.
    4. Releases the lock immediately after selecting (the lock only
       protects the SELECTION moment, not the whole API call).

  This makes both commands cooperate through real, live usage data
  instead of racing on a stale cached pointer - so all 6 keys get used
  close to their full safe capacity (5,940/day total) instead of one
  key overshooting while others sit idle.

  The existing reactive 429-handling logic (handle429/
  rotateToNextAvailableKey) is left in place as a safety net in case
  DigiKey's real-time limit differs from what local bookkeeping
  expects - it's now a backup, not the primary mechanism.

Run from the Laravel app root (same directory as artisan).
"""
import shutil
import sys
from datetime import datetime

TS = datetime.now().strftime('%Y%m%d-%H%M%S')
TARGET = 'app/Services/DigiKeyService.php'

# ---------------------------------------------------------------------------
# 1. Add the SAFE_DAILY_CAP constant next to the existing threshold constants
# ---------------------------------------------------------------------------
OLD_CONSTANTS = """    public const EXHAUSTION_THRESHOLD_SECONDS = 10800;

    public $debugCallback = null;"""

NEW_CONSTANTS = """    public const EXHAUSTION_THRESHOLD_SECONDS = 10800;

    /**
     * Treat a key as "full for today" once it hits this many calls,
     * rather than DigiKey's actual 1,000/day limit. The buffer absorbs
     * the small race window between reserving a key and the ApiUsage
     * row for that call actually being written, so concurrent
     * processes (sync-specs + sync-specs-priority running at once)
     * can't both slip a call in right at the boundary and overshoot.
     */
    public const SAFE_DAILY_CAP = 990;

    public $debugCallback = null;"""

# ---------------------------------------------------------------------------
# 2. Add the reservation method, right before "Core API call with auto-rotate"
# ---------------------------------------------------------------------------
OLD_SECTION_MARKER = """    // ── Core API call with auto-rotate ────────────────────────────────────────"""

NEW_SECTION_MARKER = """    // ── Proactive, cross-command-safe key reservation ──────────────────────────

    /**
     * Pick a key with real capacity left today and activate it, BEFORE
     * making the call - rather than waiting for DigiKey to say no.
     *
     * Safe across concurrent processes (e.g. sync-specs and
     * sync-specs-priority running at once): the selection itself is
     * protected by a real cross-process lock, and usage is read live
     * from the ApiUsage table rather than a cached counter that could
     * drift or race.
     *
     * Returns true if a usable key was found and activated, false if
     * every key is at/over the safe cap or currently 429-blocked.
     */
    public function reserveAvailableKey(): bool
    {
        $lock = Cache::lock('digikey_key_reservation', 10);

        try {
            $lock->block(5);
        } catch (\\Illuminate\\Contracts\\Cache\\LockTimeoutException $e) {
            $this->debug('Could not acquire key-reservation lock within 5s - proceeding with last-known active key');
            return true;
        }

        try {
            $utcDayStart = now('UTC')->startOfDay();

            $usageByKey = ApiUsage::where('provider', 'digikey')
                ->where('called_at', '>=', $utcDayStart)
                ->selectRaw('key_index, count(*) as calls')
                ->groupBy('key_index')
                ->pluck('calls', 'key_index');

            $best = null;
            $bestUsage = null;

            foreach (array_keys($this->keys) as $index) {
                $used = (int) ($usageByKey[$index] ?? 0);

                if ($used >= self::SAFE_DAILY_CAP) {
                    continue;
                }

                $blockedUntil = Cache::get($this->blockedUntilKey($index));
                if ($blockedUntil && now()->lt($blockedUntil)) {
                    continue;
                }

                if ($best === null || $used < $bestUsage) {
                    $best = $index;
                    $bestUsage = $used;
                }
            }

            if ($best === null) {
                $this->debug('reserveAvailableKey: every key is at/over the safe cap or blocked');
                return false;
            }

            $this->activeIndex = $best;
            Cache::put('digikey_active_key_index', $best, now()->addDays(1));
            $this->debug("reserveAvailableKey: selected key #{$best} ({$bestUsage}/" . self::SAFE_DAILY_CAP . " used today)");

            return true;
        } finally {
            $lock->release();
        }
    }

    // ── Core API call with auto-rotate ────────────────────────────────────────"""

# ---------------------------------------------------------------------------
# 3. Call reserveAvailableKey() at the top of makeProductRequest(), before
#    the existing token fetch - so every real call goes through it.
# ---------------------------------------------------------------------------
OLD_CALL_START = """    protected function makeProductRequest(string $url): ?\\Illuminate\\Http\\Client\\Response
    {
        $token = $this->getAccessToken();
        if (!$token) return null;"""

NEW_CALL_START = """    protected function makeProductRequest(string $url): ?\\Illuminate\\Http\\Client\\Response
    {
        if (!$this->reserveAvailableKey()) {
            $this->debug('makeProductRequest: no key has capacity left today - aborting call');
            return null;
        }

        $token = $this->getAccessToken();
        if (!$token) return null;"""


def patch(path, old, new, label):
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()

    count = content.count(old)
    if count == 0:
        print(f"ABORT: {label} pattern not found in {path}.")
        print("File may have changed since this patch was written - nothing modified.")
        sys.exit(1)
    if count > 1:
        print(f"ABORT: {label} pattern matched {count} times in {path}, expected exactly 1.")
        sys.exit(1)

    content = content.replace(old, new, 1)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)

    print(f"OK    {label} applied")


def main():
    backup = f"{TARGET}.bak-{TS}"
    shutil.copy2(TARGET, backup)
    print(f"Backup saved: {backup}")

    patch(TARGET, OLD_CONSTANTS, NEW_CONSTANTS, 'SAFE_DAILY_CAP constant')
    patch(TARGET, OLD_SECTION_MARKER, NEW_SECTION_MARKER, 'reserveAvailableKey() method')
    patch(TARGET, OLD_CALL_START, NEW_CALL_START, 'makeProductRequest() hook')

    print()
    print("All three changes applied to", TARGET)
    print()
    print("Next steps:")
    print("  1. php -l app/Services/DigiKeyService.php")
    print("  2. Test with a small manual run first:")
    print("     php artisan products:sync-specs 20")
    print("  3. Check the log for 'reserveAvailableKey: selected key #X (Y/990 used today)'")
    print("     lines to confirm it's picking sensibly.")
    print("  4. Once confirmed, this applies automatically to both")
    print("     products:sync-specs AND products:sync-specs-priority - no")
    print("     separate patch needed for the priority command, since both")
    print("     go through the same DigiKeyService::makeProductRequest().")


if __name__ == '__main__':
    main()
