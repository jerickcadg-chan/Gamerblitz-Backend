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
        $item = ProductItem::where('code', $data['code'])->first();
        if (!$item) {
            Log::info('LapakGaming: product Item not stored', [
                'data' => $data
            ]);
            // acknowledge callback, otherwise they will retry 3 times
            return api_status_ok('SKIPPED');
        }

        if ($item->updated_at->timestamp > $meta['unix_timestamp']) {
            // out of date, skip
            return api_status_ok('OUT OF DATE');
        }

        $item->price = $data['price'];
        // TODO: calculate reseller tier price
        $item->price_silver = $data['price'];
        $item->price_gold = $data['price'];
        $item->price_vip = $data['price'];
        $item->price_vip = $data['price'];
        $item->status = $data['status'] === 'available' ? 'active' : 'empty';
        $item->save();
        return api_status_ok('OK');
    }
}
