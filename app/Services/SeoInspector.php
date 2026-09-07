<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SeoInspector
{
    /* Check robots.txt via public URL */
    public static function robots(): bool
    {
        try {
            return Http::timeout(5)->get(url('/robots.txt'))->ok();
        } catch (\Exception $e) {
            return false;
        }
    }

    /* Detect sitemap file exposed publicly */
    public static function sitemap()
    {
        $candidates = [
            'sitemap.xml',
            'sitemap_index.xml',
            'sitemap-index.xml',
        ];

        foreach ($candidates as $file) {
            try {
                if (Http::timeout(5)->get(url('/' . $file))->ok()) {
                    return $file; // return actual filename
                }
            } catch (\Exception $e) {}
        }

        return false;
    }

    /* Last modified time (if physical file exists) */
    public static function sitemapLastUpdated(?string $file)
    {
        if (!$file) return null;

        $path = public_path($file);
        return file_exists($path)
            ? date('Y-m-d H:i', filemtime($path))
            : null;
    }

    /* SEO score calculation */
    public static function score($seo): int
    {
        $score = 0;

        if (!empty($seo->site_title))        $score += 20;
        if (!empty($seo->site_description))  $score += 20;
        if (!empty($seo->google_analytics))  $score += 30;
        if (self::robots())                  $score += 15;
        if (self::sitemap())                 $score += 15;

        return min($score, 100);
    }
}
