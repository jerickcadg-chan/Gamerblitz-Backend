<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $setting = Setting::whereIn('key', [
            'logo',
            'logo_alt',
            'brand_name',
            'title',
            'favicon',
            'keywords',
            'meta_description',
            'gtm_id',
            'popup_title',
            'popup_description',
            'popup_image',
            'popup_button_title',
            'popup_button_link',
            'popup_button_status',
            'popup_status',
            'terms',
            // 'flash_sale_expiry',
            'social_whatsapp',
            'social_youtube',
            'social_facebook',
            'social_instagram',
            'social_telegram',
            'social_tiktok',
        ])->pluck('value', 'key');

        foreach (['logo', 'logo_alt','favicon','popup_image', 'meta_image'] as $fileKey) {
            if (!empty($setting[$fileKey])) {
                $setting[$fileKey.'_url'] = Storage::url($setting[$fileKey]);
            }
        }

        return api_status_ok($setting);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
