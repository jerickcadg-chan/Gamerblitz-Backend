<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingTableSeeder extends Seeder
{
    public function run(): void
    {
        $keys = [
            // Website Setting
            'brand_name',
            'title',
            'logo',
            'favicon',
            'keywords',
            'meta_description',

            // Pop Up Setting
            'popup_title',
            'popup_description',
            'popup_image',
            'popup_button_title',
            'popup_button_link',
            'popup_button_status',
            'popup_status',

            // Flash Sale Setting
            'flash_sale_expiry',

            // Lapak Gaming
            'lapakgaming_api_key',

            // Xendit
            'xendit_secret_key',
            'xendit_callback_key',

            // Social Media Setting
            'social_whatsapp',
            'social_instagram',
            'social_facebook',
            'social_tiktok',
            'social_youtube',

            // Terms and Conditions
            'terms',
        ];

        foreach ($keys as $key) {
            Setting::firstOrCreate(
                ['key' => $key],
                ['value' => '']
            );
        }
    }
}
