#!/usr/bin/env python3
"""
Fix: product pages send conflicting robots signals.

  - Controller computes $robotsTag ('noindex, follow' for thin pages) and
    sends it correctly as an X-Robots-Tag HTTP header, but never passes it
    to the view.
  - product_details.blade.php hardcodes a SECOND, always-"index, follow"
    <meta name="robots"> tag, ignoring the controller's thin-page logic.
  - includes/front.blade.php ALSO unconditionally prints its own
    <meta name="robots" content="index, follow"> before the page's own
    @section('seo') is even yielded, so product pages end up sending
    the header + two duplicate/conflicting meta tags at once.

Fix:
  1. Controller: add 'robotsTag' => $robotsTag to the view data array.
  2. product_details.blade.php: use the dynamic $robotsTag instead of a
     hardcoded value (still includes the max-snippet/max-image-preview
     directives, just with the correct index/noindex prefix).
  3. front.blade.php: only print its own default robots meta tag when
     the page hasn't already supplied one via @section('seo'), so pages
     like product_details aren't double-tagged.

Run from the Laravel app root (same directory as artisan).
"""
import shutil
import sys
from datetime import datetime

TS = datetime.now().strftime('%Y%m%d-%H%M%S')

FILES = {
    'app/Http/Controllers/ProductController.php': [
        (
            "                    'dynamicOverview' => $dynamicOverview,\n",
            "                    'dynamicOverview' => $dynamicOverview,\n"
            "                    'robotsTag'       => $robotsTag,\n",
        ),
    ],
    'resources/views/product_details.blade.php': [
        (
            '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">\n',
            '<meta name="robots" content="{{ $robotsTag ?? \'index, follow\' }}, max-snippet:-1, max-image-preview:large, max-video-preview:-1">\n',
        ),
    ],
    'resources/views/includes/front.blade.php': [
        (
            '    <meta name="robots" content="index, follow">\n',
            "    @if (! $__env->hasSection('seo'))\n"
            '        <meta name="robots" content="index, follow">\n'
            "    @endif\n",
        ),
    ],
}


def patch_file(path, replacements):
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()

    for old, new in replacements:
        count = content.count(old)
        if count == 0:
            print(f"SKIP  {path}: pattern not found (already patched, or file changed) — no changes made")
            return False
        if count > 1:
            print(f"ABORT {path}: pattern matches {count} times, expected exactly 1 — refusing to guess")
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
        print("\nOne or more files were NOT patched. Nothing destructive happened — "
              "just re-check the file, it may have already been edited since I last read it.")
        sys.exit(1)

    print("\nAll three files patched successfully.")
    print("Next steps:")
    print("  1. Spot check: php -l app/Http/Controllers/ProductController.php")
    print("  2. Spot check: php artisan view:clear   (Blade view cache)")
    print("  3. Load a known-thin product URL and curl -I it — confirm the")
    print("     X-Robots-Tag header and the <meta name=\"robots\"> now agree.")
    print("  4. Load a known-healthy (3+ specs) product URL the same way —")
    print("     confirm it still shows index, follow everywhere.")


if __name__ == '__main__':
    main()
