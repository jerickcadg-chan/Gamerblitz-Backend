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
            'base_currency',
            'primary_color',

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
            'lapakgaming_api_url',
            'lapakgaming_ip',

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

        $defaults = [
            'brand_name'       => 'Whitelabel',
            'title'            => 'Top Up Game Online',
            'logo'             => '/img/dummy-logo.png',
            'favicon'          => '/img/dummy-favicon.svg',
            'keywords'         => 'top up game, voucher, mobile legends, free fire',
            'base_currency'    => config('app.base_currency'),
            'primary_color'    => '#445264'
        ];

        foreach ($keys as $key) {
            Setting::firstOrCreate(
                ['key' => $key],
                ['value' => $defaults[$key] ?? '']
            );
        }
    }
}
