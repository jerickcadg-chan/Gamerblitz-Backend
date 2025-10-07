<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class LapakGamingController extends Controller
{
    public function index(string $country_code)
    {
        $token = Setting::getByKey('lapakgaming_api_token');
        $baseUrl = Setting::getByKey('lapakgaming_api_url');
        $cacheKey = "lapakgaming_categories_{$country_code}";

        $products = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($token, $baseUrl, $country_code) {
            $response = Http::withToken($token)->get("$baseUrl/api/category", [
                'country_code' => $country_code,
            ]);

            if ($response->failed()) {
                throw new \Exception($response->body());
            }

            return collect($response->json('data.categories'));
        });

        $title = 'Lapak Gaming Products';
        $error = null;

        return view('lapakgaming.products', compact('products', 'country_code', 'title', 'error'));
    }
}
