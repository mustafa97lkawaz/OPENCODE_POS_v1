<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first() ?? new Setting();
        return view('settings.settings', compact('setting'));
    }

    public function update(Request $request, Setting $setting)
    {
        $validated = $request->validate([
            'store_name'     => 'nullable|string|max:255',
            'printer_type'   => 'nullable|string|max:20',
            'printer_name'   => 'nullable|string|max:255',
            'receipt_header' => 'nullable|string',
            'receipt_footer' => 'nullable|string',
            'vat_number'     => 'nullable|string|max:50',
            'currency_symbol'=> 'nullable|string|max:10',
        ]);

        $setting = Setting::first() ?? new Setting();
        $setting->fill($validated);
        $setting->save();

        return redirect()->route('settings.index')->with('Edit', 'تم تحديث الاعدادات بنجاح');
    }
}
