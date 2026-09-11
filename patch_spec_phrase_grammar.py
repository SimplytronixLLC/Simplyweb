#!/usr/bin/env python3
"""
Fix broken grammar in the per-spec phrase builder inside
generateDynamicOverview().

The current code applies "a {label} of {value}" to EVERY spec name
unconditionally, e.g.:

    "a programmable of Not Verified"   <- nonsense, "Not Verified" isn't
                                           a value that fits "a X of Y"
    "a number of circuits of 2"        <- double "of...of", because the
                                           spec name itself already
                                           contains "Number of X"

Real example seen live on MCP4231-103E/ST:
  "This component is defined by a programmable of Not Verified, a taper
   of Linear, a configuration of Potentiometer, a number of circuits of
   2, a number of taps of 129 and a resistance of 10k."

Broken, robotic-sounding grammar like this is itself a quality signal
search engines can flag - arguably worse than plain repetitive-but-
correct boilerplate.

Fix: use a "{label}: {value}" colon format instead, which is
grammatically safe regardless of the spec name's shape:

  "This component is defined by programmable: Not Verified, taper:
   Linear, configuration: Potentiometer, number of circuits: 2, number
   of taps: 129 and resistance: 10k."

Run from the Laravel app root (same directory as artisan). Safe to run
whether or not patch_description_variety.py has already been applied -
this touches a different, unrelated line.
"""
import shutil
import sys
from datetime import datetime

TS = datetime.now().strftime('%Y%m%d-%H%M%S')
TARGET = 'app/Http/Controllers/ProductController.php'

OLD_LINE = '                return "a {$label} of {$value}";'
NEW_LINE = '                return "{$label}: {$value}";'


def main():
    with open(TARGET, 'r', encoding='utf-8') as f:
        content = f.read()

    count = content.count(OLD_LINE)
    if count == 0:
        print(f"ABORT: pattern not found in {TARGET}.")
        print("Either this was already patched, or the file has changed -")
        print("nothing has been modified.")
        sys.exit(1)
    if count > 1:
        print(f"ABORT: pattern matched {count} times, expected exactly 1.")
        print("Refusing to guess - nothing has been modified.")
        sys.exit(1)

    content = content.replace(OLD_LINE, NEW_LINE, 1)

    backup = f"{TARGET}.bak-{TS}"
    shutil.copy2(TARGET, backup)
    with open(TARGET, 'w', encoding='utf-8') as f:
        f.write(content)

    print(f"OK    patched {TARGET}")
    print(f"Backup saved: {backup}")
    print()
    print("Verify: reload the MCP4231-103E/ST product page (or any product")
    print("with a 'Number of X' or status-style spec) and confirm the")
    print("Product Overview text now reads 'label: value' pairs instead of")
    print("'a label of value'.")


if __name__ == '__main__':
    main()
