<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index()
    {
        // Persist a row on first visit so the resource-bound update route
        // (settings/{setting}) resolves — otherwise a fresh install's first
        // "Save settings" 404s (no Setting #1 exists for route-model binding).
        $setting = Setting::firstOrCreate([]);
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

    /**
     * Copy the live SQLite database to Documents\PosBackups\pos-YYYYMMDD-HHmm.sqlite.
     * Only meaningful for the SQLite (desktop) connection.
     */
    public function backup()
    {
        $driver = DB::connection()->getDriverName();
        if ($driver !== 'sqlite') {
            return response()->json(['success' => false, 'message' => 'النسخ الاحتياطي متاح لقاعدة SQLite فقط'], 422);
        }

        $dbFile = DB::connection()->getDatabaseName(); // resolves the live DB_DATABASE
        if (!$dbFile || !is_file($dbFile)) {
            return response()->json(['success' => false, 'message' => 'تعذّر تحديد ملف قاعدة البيانات'], 500);
        }

        $home = getenv('USERPROFILE') ?: getenv('HOME') ?: sys_get_temp_dir();
        $dir  = rtrim($home, '\\/') . DIRECTORY_SEPARATOR . 'Documents' . DIRECTORY_SEPARATOR . 'PosBackups';
        if (!is_dir($dir) && !@mkdir($dir, 0777, true) && !is_dir($dir)) {
            return response()->json(['success' => false, 'message' => 'تعذّر إنشاء مجلد النسخ الاحتياطي'], 500);
        }

        $dest = $dir . DIRECTORY_SEPARATOR . 'pos-' . date('Ymd-Hi') . '.sqlite';
        if (!@copy($dbFile, $dest)) {
            return response()->json(['success' => false, 'message' => 'فشل نسخ قاعدة البيانات'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ النسخة الاحتياطية: ' . $dest,
            'path'    => $dest,
        ]);
    }
}
