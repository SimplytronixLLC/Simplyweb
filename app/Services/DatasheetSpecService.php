<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use App\Services\SpecIntelligenceService;

class DatasheetSpecService
{
    public function parseFromUrl($url)
    {
        $pdfPath = storage_path('app/temp_ds.pdf');

        // Step 1: Download PDF
        file_put_contents($pdfPath, file_get_contents($url));

        // Step 2: Extract text (smalot)
        $parser = new Parser();
        $pdf = $parser->parseFile($pdfPath);
        $text = $pdf->getText();

        if (!$text) return [];

        // Step 3: Clean text
        $text = $this->clean($text);

        // Step 4: Extract values
        $values = $this->extractValues($text);

        // Step 5: Build raw specs
        $rawSpecs = $this->buildSpecs($values, $text);

        // Step 6: Intelligence processing
        $intelligence = new SpecIntelligenceService();
        return $intelligence->process($rawSpecs);
    }

    // ---------------- CLEAN TEXT ----------------
    private function clean($text)
    {
        $text = strtolower($text);

        // Fix broken words (v ery → very)
        $text = preg_replace('/([a-z])\s+([a-z])/i', '$1$2', $text);

        // Merge number + unit (5 v → 5v)
        $text = preg_replace('/(\d)\s+(v|a|ma|ua|pa|hz|khz|mhz|ghz|kb|mb|gb|°c|db|ohm|ω)/i', '$1$2', $text);

        // Normalize ranges (4.5v to 5.5v → 4.5-5.5v)
        $text = preg_replace('/(\d+\.?\d*)v\s*to\s*(\d+\.?\d*)v/i', '$1-$2v', $text);

        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    // ---------------- VALUE DETECTION ----------------
    private function extractValues($text)
    {
        preg_match_all(
            '/\b\d+(\.\d+)?\s?(v|a|ma|ua|pa|fa|hz|khz|mhz|ghz|kb|mb|gb|°c|db|ohm|ω|pf|nf|uf|mh|uh|mhz|khz|dbm)\b/i',
            $text,
            $matches
        );

        return array_unique($matches[0]);
    }

    // ---------------- BUILD RAW SPECS ----------------
    private function buildSpecs($values, $text)
    {
        $specs = [];

        foreach ($values as $value) {

            $pos = stripos($text, $value);
            if ($pos === false) continue;

            // Get context window (important for meaning)
            $context = substr($text, max(0, $pos - 60), 120);

            $name = $this->detectParameter($context);

            if ($name) {
                $specs[] = [
                    'name' => $name,
                    'value' => trim($value)
                ];
            }
        }

        return $specs;
    }

    // ---------------- PARAMETER DETECTION (GENERIC) ----------------
    private function detectParameter($context)
    {
        $context = strtolower($context);

        // Remove numbers + units
        $context = preg_replace('/\d+(\.\d+)?\s?(v|a|ma|ua|pa|fa|hz|khz|mhz|ghz|kb|mb|gb|°c|db|ohm|ω|pf|nf|uf)/i', '', $context);

        // Remove symbols
        $context = preg_replace('/[^a-z\s\-]/', '', $context);

        // Normalize spaces
        $context = preg_replace('/\s+/', ' ', $context);

        $words = explode(' ', trim($context));

        // Remove noise words
        $stopWords = [
            'the','and','with','for','from','this','that',
            'typ','max','min','typical','maximum','minimum',
            'figure','table','note','test','conditions',
            'value','parameter','device','operation'
        ];

        $filtered = array_values(array_diff($words, $stopWords));

        // Take last 2–3 words (closest to value)
        $lastWords = array_slice($filtered, -3);

        if (empty($lastWords)) return null;

        return ucwords(implode(' ', $lastWords));
    }
}