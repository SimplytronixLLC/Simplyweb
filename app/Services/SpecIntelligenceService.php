<?php

namespace App\Services;

class SpecIntelligenceService
{
    public function process(array $rawSpecs)
    {
        // Step 1: normalize names
        $normalized = $this->normalizeNames($rawSpecs);

        // Step 2: group similar parameters
        $grouped = $this->groupSimilar($normalized);

        // Step 3: rank values
        $ranked = $this->rankValues($grouped);

        // Step 4: final clean
        return $this->finalize($ranked);
    }

    // ---------------- NORMALIZE ----------------
    private function normalizeNames($specs)
    {
        return array_map(function ($s) {

            $name = strtolower($s['name']);

            // clean symbols
            $name = preg_replace('/[^a-z\s\-]/', '', $name);
            $name = preg_replace('/\s+/', ' ', $name);

            // capitalize nicely
            $name = ucwords(trim($name));

            return [
                'name' => $name,
                'value' => $s['value']
            ];
        }, $specs);
    }

    // ---------------- GROUP SIMILAR ----------------
    private function groupSimilar($specs)
    {
        $groups = [];

        foreach ($specs as $spec) {

            $key = $this->fingerprint($spec['name']);

            if (!isset($groups[$key])) {
                $groups[$key] = [];
            }

            $groups[$key][] = $spec;
        }

        return $groups;
    }

    // fingerprint = loose grouping
    private function fingerprint($name)
    {
        $name = strtolower($name);

        // remove common noise words
        $name = str_replace([
            'maximum', 'minimum', 'typical', 'typ', 'max', 'min'
        ], '', $name);

        $name = preg_replace('/\s+/', '', $name);

        return $name;
    }

    // ---------------- VALUE RANKING ----------------
    private function rankValues($groups)
    {
        $result = [];

        foreach ($groups as $group) {

            $best = null;

            foreach ($group as $spec) {

                if (!$best) {
                    $best = $spec;
                    continue;
                }

                if ($this->isBetter($spec, $best)) {
                    $best = $spec;
                }
            }

            $result[] = $best;
        }

        return $result;
    }

    private function isBetter($new, $old)
    {
        $n = $this->numeric($new['value']);
        $o = $this->numeric($old['value']);

        if ($n === null || $o === null) return false;

        $name = strtolower($new['name']);

        // rules
        if (strpos($name, 'voltage') !== false ||
            strpos($name, 'current') !== false ||
            strpos($name, 'power') !== false ||
            strpos($name, 'frequency') !== false
        ) {
            return $n > $o;
        }

        if (strpos($name, 'noise') !== false ||
            strpos($name, 'offset') !== false
        ) {
            return $n < $o;
        }

        return false;
    }

    private function numeric($value)
    {
        if (preg_match('/\d+(\.\d+)?/', $value, $m)) {
            return floatval($m[0]);
        }

        return null;
    }

    // ---------------- FINAL CLEAN ----------------
    private function finalize($specs)
    {
        $output = [];

        foreach ($specs as $s) {

            if (strlen($s['name']) < 3) continue;

            $output[] = [
                'name' => $s['name'],
                'value' => strtoupper($s['value'])
            ];
        }

        return $output;
    }
}