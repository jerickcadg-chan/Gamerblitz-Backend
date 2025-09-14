<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Transformers\FlashSaleTransformer;

class FlashSaleController extends Controller
{
    public function __invoke()
    {
        $query = FlashSale::active()->get();

        return api_status_ok(transformer(
            query: $query,
            transformer: new FlashSaleTransformer(),
        ));
    }
}
