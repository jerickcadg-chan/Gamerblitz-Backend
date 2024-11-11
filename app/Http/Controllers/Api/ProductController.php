<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\ProductItem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Transformers\ProductTransformer;
use App\Transformers\ProductItemTransformer;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::active()->orderBy('created_at')->get();

        return api_status_ok(transformer($products, new ProductTransformer));
    }

    public function showProduct($product)
    {
        $product = Product::where('slug', $product)->firstOrFail();

        return api_status_ok(transformer($product, new ProductTransformer, ['productItems']));
    }

    public function getProductItems($productId)
    {
        $productItems = ProductItem::where('product_id', $productId)
            ->orderByRaw("CASE
                WHEN name RLIKE '^[0-9]' THEN 1
                WHEN name RLIKE '^[A-Za-z]' THEN 2
                ELSE 3
            END, price ASC")
            ->get();

        return api_status_ok(transformer($productItems, new ProductItemTransformer));
    }

    public function showProductItem($id)
    {
        $productItem = ProductItem::with('product')->findOrFail($id);

        return api_status_ok(transformer($productItem, new ProductItemTransformer, ['product']));
    }
}
