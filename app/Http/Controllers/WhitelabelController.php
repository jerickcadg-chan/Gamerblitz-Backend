<?php

namespace App\Http\Controllers;

use App\Constants\CountryConstant;
use App\Constants\ProviderConstant;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WhitelabelController extends Controller
{
    public function index()
    {
        $title = env('PROVIDER_WHITELABEL', 'Whitelabel') . ' Products';
        $error = null;

        $token = Setting::getByKey('whitelabel_api_token');
        $baseUrl = Setting::getByKey('whitelabel_api_url');
        $products = null;
        $existingCodes = collect();

        $cacheKey = "whitelabel_categories";
        $products = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($token, $baseUrl) {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->get("$baseUrl/partner/product");

            if ($response->failed()) {
                throw new \Exception($response->body());
            }

            return collect($response->json('payload'));
        });

        $existingCodes = Product::where('provider', ProviderConstant::WHITELABEL)
            ->pluck('provider_code');

        return view('whitelabel.products', compact('products', 'title', 'error', 'existingCodes'));
    }
}
