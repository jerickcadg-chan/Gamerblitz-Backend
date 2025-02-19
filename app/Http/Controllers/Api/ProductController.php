<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\ProductItem;
use App\Services\OrderService;
use Illuminate\Routing\Controller;
use App\Transformers\ProductTransformer;
use App\Transformers\ProductItemTransformer;
use Illuminate\Database\Eloquent\Builder;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::active()
            ->when(request('category'), function (Builder $query) {
                return $query->whereHas('productCategory', function (Builder $query) {
                    $query->where('name', request('category'));
                });
            })
            ->when(\request('name'), function ($query) {
                return $query->where('name', 'like', '%' . \request('name') . '%');
            })
            ->orderBy('ordering')
            ->get();

        return api_status_ok(transformer($products, new ProductTransformer));
    }

    public function paginate()
    {
        $products = Product::active()
            ->orderBy('created_at')
            ->when(request('category'), function (Builder $query) {
                return $query->whereHas('productCategory', function (Builder $query) {
                    $query->where('name', request('category'));
                });
            })
            ->when(\request('name'), function ($query) {
                return $query->where('name', 'like', '%' . \request('name') . '%');
            });

        return api_status_ok(paginateTransformer($products, new ProductTransformer));
    }

    public function showProduct($product)
    {
        $product = Product::where('slug', $product)->firstOrFail();

        return api_status_ok(transformer($product, new ProductTransformer));
    }

    public function getProductItems($productId)
    {
        $productItems = ProductItem::query()
            ->active()
            ->with([
                'productItemClients' => function ($query) {
                    $query->where('client_id', client()->id);
                }
            ])
            ->where('product_id', $productId)
            ->orderByRaw("CASE
                WHEN name RLIKE '^[A-Za-z]' THEN 1
                WHEN name RLIKE '^[0-9]' THEN 2
                ELSE 3
            END, price ASC")
            ->where(function ($query) {
                $query
                    ->where('stock', '>', 0)
                    ->orWhereNull('stock');
            })
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
