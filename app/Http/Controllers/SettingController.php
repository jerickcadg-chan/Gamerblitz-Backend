<?php

namespace App\Http\Controllers;

use App\Constants\CurrencyConstant;
use App\Http\Requests\SettingRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function index()
    {
        $pairs = Setting::query()->pluck('value', 'key')->toArray();

        $settings = $pairs;

        // helper URL untuk preview file
        foreach (['logo', 'logo_alt', 'favicon', 'popup_image', 'meta_image'] as $fileKey) {
            if (!empty($pairs[$fileKey])) {
                $settings[$fileKey . '_url'] = Storage::url($pairs[$fileKey]);
            }
        }

        $currencies = CurrencyConstant::all();

        return view('settings.index', compact('settings', 'currencies'));
    }

    public function update(SettingRequest $request)
    {
        $settings = $request->input('settings', []);

        // file uploads: {random}_{original_name}
        foreach (['logo', 'logo_alt', 'favicon', 'popup_image', 'meta_image'] as $key) {
            if ($request->hasFile("files.$key")) {
                $file = $request->file("files.$key");
                $filename = Str::random(10) . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('settings', $filename, 'public');
                $settings[$key] = $path;
            }

            // Kalau user klik clear (frontend kirim null atau string kosong)
            elseif ($request->input("clear_files.$key") === 'true') {
                $settings[$key] = null;
            }
        }

        // normalisasi on/off
        foreach (['popup_button_status', 'popup_status'] as $boolKey) {
            if (isset($settings[$boolKey])) {
                $val = strtolower((string)$settings[$boolKey]);
                $settings[$boolKey] = in_array($val, ['on', '1', 'true', 'yes']) ? 'on' : 'off';
            }
        }

        // simpan
        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => is_string($value) ? $value : (string) $value]);
        }

        toast(alert_created_text('Setting'), 'success');

        return redirect()->route('setting.index');
    }
}
