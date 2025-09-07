<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductItemPriceController extends Controller
{
    public function index(): View
    {
        $title = 'Product Item Price';

        $products = Product::all();

        return view('product_items.price-form', compact('title', 'products'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'product_id' => ['required'],
            'margin' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'margin_silver' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'margin_gold' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'margin_vip' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $data = [
            'margin'        => $request->margin,
            'margin_silver' => $request->margin_silver,
            'margin_gold'   => $request->margin_gold,
            'margin_vip'    => $request->margin_vip,
        ];

        $data = array_filter($data, fn($value) => !is_null($value));

        ProductItem::where('product_id', $request->product_id)->update($data);

        toast('Updated product item price', 'success');
        return redirect()->route('product_item.index');
    }
}
