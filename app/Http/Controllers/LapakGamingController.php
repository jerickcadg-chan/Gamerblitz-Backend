<?php

namespace App\Http\Controllers;

use App\Constants\CountryConstant;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class LapakGamingController extends Controller
{
    public function index()
    {
        $title = 'Lapak Gaming Products';
        $error = null;

        $token = Setting::getByKey('lapakgaming_api_token');
        $baseUrl = Setting::getByKey('lapakgaming_api_url');
        $countryCode = request('country');
        $countries = CountryConstant::all();

        if ($countryCode) {
            $cacheKey = "lapakgaming_categories_{$countryCode}";
            $products = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($token, $baseUrl, $countryCode) {
                $response = Http::withToken($token)->get("$baseUrl/api/category", [
                    'country_code' => $countryCode,
                ]);
    
                if ($response->failed()) {
                    throw new \Exception($response->body());
                }
    
                return collect($response->json('data.categories'));
            });
        } else {
            $products = null;
        }

        return view('lapakgaming.products', compact('products', 'countries', 'title', 'error'));
    }
}
