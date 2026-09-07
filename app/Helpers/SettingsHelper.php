<?php

namespace App\Helpers;

use App\Models\Settings;
use Illuminate\Support\Facades\Cache;

class SettingsHelper
{
    public static function get(string $key, $default = null)
    {
        $settings = Cache::remember('app_settings_row', 3600, function () {
            return Settings::where('id', 1)->first();
        });

        return $settings->{$key} ?? $default;
    }
}
