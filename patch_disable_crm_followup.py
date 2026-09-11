#!/usr/bin/env python3
"""
Add a kill switch for the automated RFQ follow-up email cadence
(CrmFollowupMail, sent via CrmSyncDaily's enrollQuoteLeadsAfter30Days()
and sendDueFollowups()).

Requested: stop these emails from going out. 7,213 already-enrolled
contacts were paused directly in the DB (automation_enabled = false),
but that alone doesn't stop tomorrow's 08:00 cron from auto-enrolling a
fresh batch of 30-day-old quote leads and starting the cycle again.

This patch adds 'followup_cadence_enabled' => false to config/crm.php
and gates both methods behind it in CrmSyncDaily::handle(), so:
  - Nothing sends or auto-enrolls while it's false (the new default)
  - Re-enabling later is a one-line config change back to true - no
    code changes needed
  - importQuotes() and importHighEngagementVisitors() (data import,
    unrelated to sending emails) are untouched and keep running daily
    as before

Does NOT touch the separate crm:send-winback-followups command/cadence
- only the one identified as the source of the RFQ follow-up email.

Run from the Laravel app root (same directory as artisan).
"""
import shutil
import sys
from datetime import datetime

TS = datetime.now().strftime('%Y%m%d-%H%M%S')

CRM_SYNC_TARGET = 'app/Console/Commands/CrmSyncDaily.php'
CONFIG_TARGET = 'config/crm.php'

OLD_HANDLE = '''    public function handle(): int
    {
        $this->importQuotes();
        $this->importHighEngagementVisitors();
        $this->enrollQuoteLeadsAfter30Days();
        $this->sendDueFollowups();

        return self::SUCCESS;
    }'''

NEW_HANDLE = '''    public function handle(): int
    {
        $this->importQuotes();
        $this->importHighEngagementVisitors();

        if (config('crm.followup_cadence_enabled', false)) {
            $this->enrollQuoteLeadsAfter30Days();
            $this->sendDueFollowups();
        } else {
            $this->info('RFQ follow-up cadence is disabled (crm.followup_cadence_enabled) - skipping enrollment and sends.');
        }

        return self::SUCCESS;
    }'''

OLD_CONFIG = """<?php
return [
    'followup_cadence_days' => [0, 3, 7, 14],
    'visitor_engagement_threshold' => 50,
];"""

NEW_CONFIG = """<?php
return [
    // Master on/off switch for the automated RFQ follow-up email
    // sequence (CrmFollowupMail). Set to true to re-enable.
    'followup_cadence_enabled' => false,
    'followup_cadence_days' => [0, 3, 7, 14],
    'visitor_engagement_threshold' => 50,
];"""


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
    backup = f"{path}.bak-{TS}"
    shutil.copy2(path, backup)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)

    print(f"OK    {path} patched (backup: {backup})")


def main():
    patch(CRM_SYNC_TARGET, OLD_HANDLE, NEW_HANDLE, 'handle()')
    patch(CONFIG_TARGET, OLD_CONFIG, NEW_CONFIG, 'config')

    print()
    print("Follow-up cadence is now OFF by default. Next steps:")
    print("  1. php -l app/Console/Commands/CrmSyncDaily.php")
    print("  2. php -l config/crm.php")
    print("  3. php artisan config:clear   (config is often cached in production)")
    print("  4. To re-enable later: edit config/crm.php, set")
    print("     'followup_cadence_enabled' => true, then php artisan config:clear")


if __name__ == '__main__':
    main()
