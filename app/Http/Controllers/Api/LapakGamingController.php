<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class LapakGamingController extends Controller
{
    public function productUpdateCallback(Request $request): Response
    {
        // {
        //     "data": {
        //         "code": "ML-100-S102",
        //         "name": "100 Diamonds Automation",
        //         "provider_code": "S102",
        //         "price": 9565,
        //     "status": "available"
        // },
        //     "meta": {
        //         "level": "master",
        //         "unix_timestamp": 1707470882
        //     }
        // }
        $data = $request->input('data');
        $meta = $request->input('meta');

        $productItem = ProductItem::where('code', $data['code'])->first();

        if (!$productItem) {
            Log::info('LapakGaming: product Item not stored', [
                'data' => $data
            ]);
            // acknowledge callback, otherwise they will retry 3 times
            return api_status_ok('SKIPPED');
        }

        if ($productItem->sync_at->timestamp > $meta['unix_timestamp']) {
            // out of date, skip
            return api_status_ok('OUT OF DATE');
        }

        $product = $productItem->product;

        $marginPublicUser = $productItem->margin ?: $product->markup_user;
        $marginSilver = $productItem->margin_silver ?: $product->markup_reseller_silver;
        $marginGold = $productItem->margin_gold ?: $product->markup_reseller_gold;
        $marginVip = $productItem->margin_vip ?: $product->markup_reseller_vip;

        $productItem->margin = $marginPublicUser;
        $productItem->margin_silver = $marginSilver;
        $productItem->margin_gold = $marginGold;
        $productItem->margin_vip = $marginVip;
        $productItem->status = $data['status'] === 'available' ? 'active' : 'empty';
        $productItem->sync_at = now();
        $productItem->save();
        return api_status_ok('OK');
    }
}
