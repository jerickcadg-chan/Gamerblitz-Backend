<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\ProductItem;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Transformers\ProductTransformer;
use App\Transformers\ProductItemTransformer;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at')->active()
            ->when(\request('name'), function ($query) {
                return $query->where('name', 'like', '%'. \request('name') .'%');
            })
            ->get();

        return api_status_ok(transformer($products, new ProductTransformer));
    }

    public function showProduct($product)
    {
        $product = Product::where('slug', $product)->firstOrFail();

        return api_status_ok(transformer($product, new ProductTransformer));
    }

    public function getProductItems($productId)
    {
        $productItems = ProductItem::where('product_id', $productId)
            ->orderByRaw("CASE
                WHEN name RLIKE '^[A-Za-z]' THEN 1
                WHEN name RLIKE '^[0-9]' THEN 2
                ELSE 3
            END, price ASC")
            ->where('stock', '>', 0)
            ->get();

        return api_status_ok(transformer($productItems, new ProductItemTransformer));
    }

    public function showProductItem($id)
    {
        $productItem = ProductItem::with('product')->findOrFail($id);

        return api_status_ok(transformer($productItem, new ProductItemTransformer, ['product']));
    }

    public function test(OrderService $orderService)
    {
        return $orderService->calculateJokiMLPrice(\request('start_rank'), \request('start_grade'), \request('start_stars'), \request('end_rank'), \request('end_grade'), \request('end_stars'));
    }
}
