<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class LogoController extends Controller
{
    public function index()
    {
        $data = Settings::where('id', 1)->first();
        return view('admin.others.logo', compact('data'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|max:2048',
            'footer_logo' => 'nullable|image|max:2048',
            'icon' => 'nullable|image|max:2048',
        ]);

        $updates = [];
        $uploadPath = public_path('uploads');

        foreach (['logo', 'footer_logo', 'icon'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = time() . $file->getClientOriginalName();
                $file->move($uploadPath, $filename);
                $updates[$field] = $filename;
            }
        }

        if (!empty($updates)) {
            Settings::where('id', 1)->update($updates);
            Cache::forget('app_settings_row');
        }

        Session::flash('message', 'Logo updated successfully.');
        return redirect('admin/logo');
    }
}
