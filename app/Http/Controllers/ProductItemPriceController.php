<?php

namespace App\Http\Controllers;

use App\Models\ProductItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductItemPriceController extends Controller
{
    public function index(): View
    {
        return view('product_item_price.index');
    }

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'product_item_ids' => [
                    Rule::requiredIf(function () use ($request) {
                        return !$request->update_all;
                    }),
                    'array'
                ],
                'margin' => ['required', 'numeric', 'min:0', 'max:100'],
                'margin_silver' => ['required', 'numeric', 'min:0', 'max:100'],
                'margin_gold' => ['required', 'numeric', 'min:0', 'max:100'],
                'margin_vip' => ['required', 'numeric', 'min:0', 'max:100'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $query = $request->update_all
        ? ProductItem::query()
        : ProductItem::whereIn('id', $request->product_item_ids);

        $query->update([
            'margin'          => $request->margin,
            'margin_silver' => $request->margin_silver,
            'margin_gold' => $request->margin_gold,
            'margin_vip' => $request->margin_vip,
            'updated_at'      => now(),
        ]);

        return response()->json([
            'message' => 'Product price updated',
            'success' => true,
        ]);
    }
}
