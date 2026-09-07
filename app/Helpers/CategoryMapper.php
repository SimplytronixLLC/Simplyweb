<?php

namespace App\Helpers;

use DB;

class CategoryMapper
{
    public static function map()
    {
        // Prevent timeout & memory crash
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        // Load categories once
        $categories = DB::table('categories')->get();

        DB::table('cached_products')
            ->orderBy('id')
            ->chunk(200, function ($products) use ($categories) {

                foreach ($products as $product) {

                    $raw = $product->raw_data;
                    if (!$raw) continue;

                    // Fix broken/double JSON
                    $raw = self::cleanJson($raw);
                    $data = json_decode($raw, true);

                    if (!$data) continue;

                    // ----------------------------
                    // STEP 1: Extract Category Name
                    // ----------------------------
                    $categoryName = null;

                    // Case 1: "Category": "XYZ"
                    if (isset($data['Category'])) {
                        if (is_array($data['Category'])) {
                            $categoryName = $data['Category']['Name'] ?? null;
                        } else {
                            $categoryName = $data['Category'];
                        }
                    }

                    // Case 2: fallback from description
                    if (!$categoryName && isset($data['Description'])) {
                        if (is_array($data['Description'])) {
                            $categoryName = $data['Description']['ProductDescription'] ?? null;
                        } else {
                            $categoryName = $data['Description'];
                        }
                    }

                    if (!$categoryName) continue;

                    $categoryName = trim($categoryName);

                    // ----------------------------
                    // STEP 2: Custom Mapping Fix
                    // ----------------------------
                    $categoryName = self::mapCustom($categoryName);

                    // ----------------------------
                    // STEP 3: Match with DB Categories
                    // ----------------------------
                    $matched = $categories->first(function ($cat) use ($categoryName) {
                        return stripos($categoryName, $cat->name) !== false;
                    });

                    if (!$matched) continue;

                    // ----------------------------
                    // STEP 4: Build hierarchy
                    // ----------------------------
                    $level1 = null;
                    $level2 = null;
                    $level3 = null;

                    $current = $matched;

                    if ($current->level == 3) {
                        $level3 = $current->id;

                        $parent = $categories->firstWhere('id', $current->parent_id);
                        if ($parent) {
                            $level2 = $parent->id;

                            $grand = $categories->firstWhere('id', $parent->parent_id);
                            if ($grand) {
                                $level1 = $grand->id;
                            }
                        }
                    }

                    if ($current->level == 2) {
                        $level2 = $current->id;

                        $parent = $categories->firstWhere('id', $current->parent_id);
                        if ($parent) {
                            $level1 = $parent->id;
                        }
                    }

                    if ($current->level == 1) {
                        $level1 = $current->id;
                    }

                    // ----------------------------
                    // STEP 5: Update DB
                    // ----------------------------
                    DB::table('cached_products')
                        ->where('id', $product->id)
                        ->update([
                            'category' => $matched->name,
                            'category_id' => $matched->id,
                            'category_level_1' => $level1,
                            'category_level_2' => $level2,
                            'category_level_3' => $level3,
                        ]);
                }
            });

        echo "Category mapping completed\n";
    }

    /**
     * Fix double encoded JSON
     */
    private static function cleanJson($raw)
    {
        if (is_string($raw) && substr($raw, 0, 1) === '"') {
            $raw = stripslashes(trim($raw, '"'));
        }

        return $raw;
    }

    /**
     * Custom mapping for mismatched Mouser categories
     */
    private static function mapCustom($name)
    {
        $map = [
            'Power Management Modules' => 'Interface ICs',
            'Gate Drivers' => 'Interface ICs',
            'Motor/Motion/Ignition Controllers & Drivers' => 'Interface ICs',
            'LED Lighting Driver ICs' => 'Interface ICs',
        ];

        foreach ($map as $key => $value) {
            if (stripos($name, $key) !== false) {
                return $value;
            }
        }

        return $name;
    }
}