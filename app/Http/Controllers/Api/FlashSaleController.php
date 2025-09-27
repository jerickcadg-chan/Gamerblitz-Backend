<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\Setting;
use App\Transformers\FlashSaleTransformer;

class FlashSaleController extends Controller
{
    public function __invoke()
    {
        $baseCurrency = Setting::getBaseCurrency();
        $currencyCode = request('currency_code') ? request('currency_code') : $baseCurrency;
        $exchangeRate = get_exchange_rate($baseCurrency, $currencyCode);

        $query = FlashSale::active()->with('productItem')->get();

        return api_status_ok(transformer(
            query: $query,
            transformer: new FlashSaleTransformer($exchangeRate),
        ));
    }
}
