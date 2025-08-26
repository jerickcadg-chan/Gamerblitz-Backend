<?php

namespace App\Http\Controllers;

use App\Models\ProductItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        /** @var \App\Models\Client $client */
        $client = Auth::user()->client;

        if (!$request->update_all) {
            $productItemIds = $request->product_item_ids;
            $productItems = ProductItem::whereIn('id', $productItemIds)->get();
        } else {
            $productItems = ProductItem::all();
        }

        foreach ($productItems as $productItem) {
            DB::table('product_item_client')->updateOrInsert(
                [
                    'product_item_id' => $productItem->id,
                    'client_id' => $client->id,
                ],
                [
                    'margin' => $request->margin,
                    'reseller_margin' => $request->margin_reseller,
                    'updated_at' => now(),
                ]
            );
        }

        return response()->json([
            'message' => 'Product price updated',
            'success' => true,
        ]);
    }
}
