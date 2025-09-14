<?php

namespace App\Http\Controllers\Api;

use App\Models\Discount;
use App\Transformers\DiscountTransformer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::active()->get();

        return api_status_ok(transformer($discounts, new DiscountTransformer));
    }

    public function checkDiscountCode(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);

        $promo = Discount::active()->where('code', $request->code)->first();

        if (empty($promo)) {
            return api_status_warning('Discount not found', 201);
        }

        return api_status_ok(transformer($promo, new DiscountTransformer));
    }

    public function availableDiscount()
    {
        $productId = request('product_id');

        $discounts = Discount::active();

        $discounts->where('product_type', Discount::ALL);

        if ($productId) {
            $discountIds = DB::table('discount_product')
                ->where('productable_type', \App\Models\Product::class)
                ->where('productable_id', $productId)
                ->pluck('discount_id');

            $discounts->orWhere(function ($q) use ($discountIds) {
                $q->where('product_type', Discount::PRODUCT_TYPE)
                    ->whereIn('id', $discountIds);
            });
        }

        return api_status_ok(paginateTransformer($discounts, new DiscountTransformer));
    }
}
