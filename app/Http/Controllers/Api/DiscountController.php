<?php

namespace App\Http\Controllers\Api;

use App\Models\Discount;
use App\Transformers\DiscountTransformer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class DiscountController extends Controller
{
    public function checkDiscountCode(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'product_item_id' => 'nullable|exists:product_items,id',

        ]);

        $validDiscountCode = validate_discount_code($request->code, $request->product_item_id);

        if ($validDiscountCode['status'] === false) {
            return api_status_warning($validDiscountCode['message']);
        }

        return api_status_ok(transformer($validDiscountCode['discount'], new DiscountTransformer));
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
