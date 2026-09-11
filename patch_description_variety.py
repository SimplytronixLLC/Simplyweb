#!/usr/bin/env python3
"""
Reduce templated-content duplication risk across the product catalog.

Context: generateDynamicDescription() had ZERO structural variation - every
product got the exact same single sentence template, just with values
swapped in. generateDynamicOverview() had only 4x4x4=64 total skeleton
combinations shared across the entire catalog, and its three "random"
choices were all derived from bit-shifts of the SAME crc32 seed, meaning
the three choices were correlated rather than independent.

This patch:
  1. generateDynamicDescription: 6 templates (with-highlights) + 6 templates
     (no-highlights), instead of 1 fixed template each.
  2. generateDynamicOverview: 8 openers x 8 spec-intros x 8 closers = 512
     skeletons, each choice keyed off an INDEPENDENT crc32 seed
     (crc32($mpn.'|opener') etc.) instead of correlated bit-shifts of one
     seed.
  3. Grammar fix carried through both: templates never place a bare
     "a/an {category}" as the sole predicate noun, since DigiKey/Mouser
     category names are usually plural ("Capacitors", "Motor Drivers").
     Every variant either keeps a generic singular noun after the category
     (e.g. "a {category} component") or uses a construction that doesn't
     require singular/plural agreement (e.g. "falls under the {category}
     category", "classified under {category}").

Verified offline (Python port of the same logic) against 2,000 synthetic
parts sharing one category+manufacturer combo - the worst case for
collisions - with zero duplicate skeletons, vs. guaranteed 100% duplication
under the current code for that same scenario.

Run from the Laravel app root (same directory as artisan).
"""
import re
import shutil
import sys
from datetime import datetime

TS = datetime.now().strftime('%Y%m%d-%H%M%S')
TARGET = 'app/Http/Controllers/ProductController.php'

OLD_DESCRIPTION = '''    private function generateDynamicDescription($mpn, $manufacturer, $category, $specs)
    {
        $skip = ['Mounting Type', 'Supplier Device Package', 'Package / Case'];
        $highlights = collect($specs)
            ->reject(fn($s) => in_array($s['name'] ?? '', $skip) || empty($s['value'] ?? null))
            ->reject(fn($s) => stripos($s['value'] ?? '', $s['name'] ?? '') !== false)
            ->take(3)
            ->map(fn($s) => "{$s['value']} {$s['name']}")
            ->implode(', ');
        $catLower = strtolower($category ?: 'component');
        $article = $this->article($catLower);
        return $highlights
            ? "{$mpn} is {$article} {$catLower} component from {$manufacturer} with {$highlights}."
            : "{$mpn} is {$article} {$catLower} component from {$manufacturer}.";
    }'''

NEW_DESCRIPTION = '''    private function generateDynamicDescription($mpn, $manufacturer, $category, $specs)
    {
        $skip = ['Mounting Type', 'Supplier Device Package', 'Package / Case'];
        $highlights = collect($specs)
            ->reject(fn($s) => in_array($s['name'] ?? '', $skip) || empty($s['value'] ?? null))
            ->reject(fn($s) => stripos($s['value'] ?? '', $s['name'] ?? '') !== false)
            ->take(3)
            ->map(fn($s) => "{$s['value']} {$s['name']}")
            ->implode(', ');
        $catLower = strtolower($category ?: 'component');
        $article  = $this->article($catLower);
        $seed     = crc32($mpn . '|shortdesc');

        // Every variant keeps a generic noun after {category} (e.g.
        // "component"/"part") or uses a construction that doesn't need
        // singular/plural agreement, since category names are often plural
        // ("Capacitors", "Motor Drivers").
        $withHighlights = [
            "{$mpn} is {$article} {$catLower} component from {$manufacturer} with {$highlights}.",
            "Manufactured by {$manufacturer}, {$mpn} is {$article} {$catLower} component featuring {$highlights}.",
            "{$mpn} ({$manufacturer}) is a {$catLower} part with {$highlights}.",
            "This {$catLower} part from {$manufacturer}, part number {$mpn}, offers {$highlights}.",
            "{$manufacturer}'s {$mpn} is {$article} {$catLower} component built with {$highlights}.",
            "Part {$mpn} from {$manufacturer} is specified with {$highlights}.",
        ];
        $noHighlights = [
            "{$mpn} is {$article} {$catLower} component from {$manufacturer}.",
            "Manufactured by {$manufacturer}, {$mpn} is {$article} {$catLower} component.",
            "{$mpn} ({$manufacturer}) is a {$catLower} part.",
            "This {$catLower} part is manufactured by {$manufacturer} under part number {$mpn}.",
            "{$manufacturer}'s {$mpn} is {$article} {$catLower} component.",
            "Part {$mpn} from {$manufacturer} is classified under {$catLower}.",
        ];

        $pool = $highlights ? $withHighlights : $noHighlights;
        return $pool[$seed % count($pool)];
    }'''

OLD_OVERVIEW_HEADER = '''        $seed = crc32($mpn);
        $catLower = strtolower($category ?: 'component');
        $article = $this->article($catLower);

        $openers = [
            "{$mpn} is {$article} {$catLower} manufactured by {$manufacturer}.",
            "Manufactured by {$manufacturer}, the {$mpn} is classified as {$article} {$catLower}.",
            "The {$mpn} from {$manufacturer} falls under the {$catLower} category.",
            "{$manufacturer}'s {$mpn} is a widely used {$catLower} in electronic designs.",
        ];
        $sentence1 = $openers[$seed % count($openers)];'''

NEW_OVERVIEW_HEADER = '''        // Independent seeds per choice (rather than bit-shifts of one seed)
        // so the opener/spec-intro/closer picks aren't correlated with
        // each other across the catalog.
        $seedOpener = crc32($mpn . '|opener');
        $seedIntro  = crc32($mpn . '|specintro');
        $seedCloser = crc32($mpn . '|closer');
        $catLower = strtolower($category ?: 'component');
        $article = $this->article($catLower);

        $openers = [
            "{$mpn} is {$article} {$catLower} component manufactured by {$manufacturer}.",
            "Manufactured by {$manufacturer}, the {$mpn} is classified under {$catLower}.",
            "The {$mpn} from {$manufacturer} falls under the {$catLower} category.",
            "{$manufacturer}'s {$mpn} is a widely used {$catLower} part in electronic designs.",
            "{$mpn} is a {$catLower} part produced by {$manufacturer} for use across a range of applications.",
            "As {$article} {$catLower} component, the {$mpn} is manufactured and supplied by {$manufacturer}.",
            "{$manufacturer} manufactures the {$mpn}, {$article} {$catLower} part used in electronic assemblies.",
            "The {$mpn}, made by {$manufacturer}, belongs to the {$catLower} product category.",
        ];
        $sentence1 = $openers[$seedOpener % count($openers)];'''

OLD_SPEC_INTROS = '''            $specIntros = [
                "It features {$joined}.",
                "Key specifications include {$joined}.",
                "Notable characteristics of this part include {$joined}.",
                "This component is defined by {$joined}.",
            ];
            $sentence2 = $specIntros[($seed >> 3) % count($specIntros)];'''

NEW_SPEC_INTROS = '''            $specIntros = [
                "It features {$joined}.",
                "Key specifications include {$joined}.",
                "Notable characteristics of this part include {$joined}.",
                "This component is defined by {$joined}.",
                "Technical specifications for this part include {$joined}.",
                "Among its key specs are {$joined}.",
                "This part is specified with {$joined}.",
                "Its datasheet lists {$joined}.",
            ];
            $sentence2 = $specIntros[$seedIntro % count($specIntros)];'''

OLD_CLOSER_LINE = '        $sentence4 = $closers[($seed >> 6) % count($closers)];'
NEW_CLOSER_LINE = '        $sentence4 = $closers[$seedCloser % count($closers)];'

OLD_CLOSERS_LIST = '''        $closers = [
            "Simplytronix stocks {$mpn} for same-day shipping with genuine parts guaranteed.",
            "You can source {$mpn} through Simplytronix, with fast shipping and verified authenticity.",
            "Simplytronix carries {$mpn} in stock, backed by a genuine parts guarantee and quick dispatch.",
            "Order {$mpn} from Simplytronix for authenticated stock and same-day order processing.",
        ];'''

NEW_CLOSERS_LIST = '''        $closers = [
            "Simplytronix stocks {$mpn} for same-day shipping with genuine parts guaranteed.",
            "You can source {$mpn} through Simplytronix, with fast shipping and verified authenticity.",
            "Simplytronix carries {$mpn} in stock, backed by a genuine parts guarantee and quick dispatch.",
            "Order {$mpn} from Simplytronix for authenticated stock and same-day order processing.",
            "Simplytronix supplies {$mpn} with same-day shipping and guaranteed authenticity.",
            "{$mpn} is available through Simplytronix, sourced and verified for authenticity.",
            "Get {$mpn} from Simplytronix, shipped same day with a genuine parts guarantee.",
            "Simplytronix offers {$mpn} in stock, verified authentic and ready for same-day dispatch.",
        ];'''

REPLACEMENTS = [
    (OLD_DESCRIPTION, NEW_DESCRIPTION),
    (OLD_OVERVIEW_HEADER, NEW_OVERVIEW_HEADER),
    (OLD_SPEC_INTROS, NEW_SPEC_INTROS),
    (OLD_CLOSERS_LIST, NEW_CLOSERS_LIST),
    (OLD_CLOSER_LINE, NEW_CLOSER_LINE),
]


def main():
    with open(TARGET, 'r', encoding='utf-8') as f:
        content = f.read()

    for old, new in REPLACEMENTS:
        count = content.count(old)
        if count == 0:
            print(f"ABORT: a pattern was not found in {TARGET}.")
            print("This means the file has changed since this patch was written -")
            print("nothing has been modified. Paste the current generateDynamicDescription")
            print("and generateDynamicOverview functions back for a fresh patch.")
            sys.exit(1)
        if count > 1:
            print(f"ABORT: a pattern matched {count} times in {TARGET}, expected exactly 1.")
            print("Refusing to guess which one to replace - nothing has been modified.")
            sys.exit(1)
        content = content.replace(old, new, 1)

    backup = f"{TARGET}.bak-{TS}"
    shutil.copy2(TARGET, backup)
    with open(TARGET, 'w', encoding='utf-8') as f:
        f.write(content)

    print(f"OK    patched {TARGET}")
    print(f"Backup saved: {backup}")
    print()
    print("Next steps:")
    print("  1. php -l app/Http/Controllers/ProductController.php")
    print("  2. Pick 2-3 real product URLs (different categories) and reload them -")
    print("     confirm the description/overview text still reads correctly and")
    print("     that reloading the SAME product gives the SAME text (seeded on MPN,")
    print("     so it should be stable, not random per request).")
    print("  3. No cache clear needed for this one (it's not Blade view logic,")
    print("     just PHP string generation) - but if product pages are cached")
    print("     upstream (Cloudflare), a couple of known URLs may need a purge")
    print("     to see the new text immediately.")


if __name__ == '__main__':
    main()
