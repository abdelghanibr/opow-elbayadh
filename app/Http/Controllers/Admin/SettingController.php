<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'wilaya_ar'       => 'required|string|max:255',
            'wilaya_fr'       => 'required|string|max:255',
            'office_short'    => 'required|string|max:255',
            'office_label_fr' => 'required|string|max:500',
            'contact_email'   => 'required|email|max:255',
            'contact_phone'   => 'required|string|max:20',
            'contact_place'   => 'required|string|max:500',
            'app_name_ar'     => 'required|string|max:255',
        ]);

        foreach ($request->only([
            'wilaya_ar', 'wilaya_fr', 'office_short', 'office_label_fr',
            'contact_email', 'contact_phone', 'contact_place', 'app_name_ar',
        ]) as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'تم تحديث الإعدادات بنجاح');
    }
}
