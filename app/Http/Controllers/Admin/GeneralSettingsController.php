<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GeneralSettingsController extends Controller
{
    public function index()
    {
        $settings = Settings::where('id', 1)->first();
        return view('admin.general-settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keyword' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zipcode' => 'nullable|string|max:20',
        ]);

        Settings::where('id', 1)->update($data);
        Cache::forget('app_settings_row');

        return back()->with('success', 'Settings updated.');
    }
}
