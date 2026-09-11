#!/usr/bin/env python3
"""
Block public access to six loose diagnostic/test scripts sitting in the
webroot, without deleting them. Since document root = app root on this
shared-hosting setup, any file here is directly reachable by URL. These
six have real side effects or leak data if hit unauthenticated:

  check_contact.php        - dumps a real customer's full CRM record
  reset_false_cadences.php - leaks contact names/emails even in dry-run
  phpcheck.php              - reveals server hardening details
  opcache-clear.php         - lets anyone force an opcache reset
  twilio-test.php           - places a real outbound phone call
  digikey_test.php          - burns DigiKey API quota

Adds RedirectMatch 404 rules to .htaccess, in the same style as the
existing .git/.env/.htpasswd blocks, so these return a plain 404 instead
of executing - while the files themselves stay on disk for future use
via SSH/CLI if needed.

Run from the Laravel app root (same directory as .htaccess).
"""
import shutil
import sys
from datetime import datetime

TS = datetime.now().strftime('%Y%m%d-%H%M%S')
TARGET = '.htaccess'

OLD_BLOCK = """# ── Block sensitive folders from public access ─────────────────
RedirectMatch 404 /\\.git
RedirectMatch 404 /\\.env
RedirectMatch 404 /\\.htpasswd"""

NEW_BLOCK = """# ── Block sensitive folders from public access ─────────────────
RedirectMatch 404 /\\.git
RedirectMatch 404 /\\.env
RedirectMatch 404 /\\.htpasswd
# ── Block loose diagnostic/test scripts (kept on disk, not public) ──
RedirectMatch 404 ^/check_contact\\.php$
RedirectMatch 404 ^/reset_false_cadences\\.php$
RedirectMatch 404 ^/phpcheck\\.php$
RedirectMatch 404 ^/opcache-clear\\.php$
RedirectMatch 404 ^/twilio-test\\.php$
RedirectMatch 404 ^/digikey_test\\.php$"""


def main():
    with open(TARGET, 'r', encoding='utf-8') as f:
        content = f.read()

    if 'Block loose diagnostic/test scripts' in content:
        print("SKIP: .htaccess already has this block - no changes made.")
        sys.exit(0)

    count = content.count(OLD_BLOCK)
    if count == 0:
        print("ABORT: expected block not found in .htaccess.")
        print("The file may have changed since this patch was written -")
        print("nothing has been modified. Paste current .htaccess for a fresh patch.")
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
    print("Verify by requesting each blocked URL and confirming a 404:")
    print("  curl -sI https://simplytronix.com/check_contact.php | head -1")
    print("  curl -sI https://simplytronix.com/reset_false_cadences.php | head -1")
    print("  curl -sI https://simplytronix.com/phpcheck.php | head -1")
    print("  curl -sI https://simplytronix.com/opcache-clear.php | head -1")
    print("  curl -sI https://simplytronix.com/twilio-test.php | head -1")
    print("  curl -sI https://simplytronix.com/digikey_test.php | head -1")
    print("Each should now return 'HTTP/2 404' instead of 200.")
    print()
    print("No cache clear or restart needed - .htaccess takes effect on the")
    print("next request. The files themselves are untouched on disk.")


if __name__ == '__main__':
    main()
