<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\Setting;
use App\Services\OrderService;
use App\Transformers\ProductItemTransformer;
use App\Transformers\ProductTransformer;
use Illuminate\Database\Eloquent\Builder;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::select([
            'id',
            'ordering',
            'name',
            'code',
            // 'input_format',
            'product_category_id',
            // 'description',
            'company',
            // 'how_to_order',
            'slug',
            'status',
            'provider',
            'provider_code',
            'provider_country',
            'markup_user',
            'markup_reseller_silver',
            'markup_reseller_gold',
            'markup_reseller_vip',
            'default_picture',
            'default_cover',
            'meta_title',
            'meta_keyword',
            'meta_description',
            'deleted_at',
            'created_at',
            'updated_at',
            'check_uid',
            'is_raw_description',
        ])
            ->active()
            ->when(request('category'), function (Builder $query) {
                return $query->whereHas('productCategory', function (Builder $query) {
                    $query->where('name', request('category'));
                });
            })
            ->when(request('exclude_category'), function (Builder $query) {
                $excluded = explode(',', request('exclude_category'));
                $query->whereDoesntHave('productCategory', function (Builder $query) use ($excluded) {
                    $query->whereIn('name', $excluded);
                });
            })
            ->when(\request('name'), function ($query) {
                return $query->where('name', 'like', '%'.\request('name').'%');
            })
            ->orderByRaw('COALESCE(ordering, 999999) ASC')
            ->when(request('limit'), function ($query) {
                return $query->limit(request('limit'));
            })
            ->get();

        return api_status_ok(transformer($products, new ProductTransformer()));
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
                return $query->where('name', 'like', '%'.\request('name').'%');
            });

        return api_status_ok(paginateTransformer($products, new ProductTransformer()));
    }

    public function showProduct($product)
    {
        $product = Product::where('slug', $product)->firstOrFail();

        return api_status_ok(transformer($product, new ProductTransformer()));
    }

    public function getProductItems($productId)
    {
        $baseCurrency = Setting::getBaseCurrency();
        $currencyCode = request('currency_code') ? request('currency_code') : $baseCurrency;

        $productItems = ProductItem::query()
            ->with('product.productCategory')
            ->filter($this->filter())
            ->active()
            ->where('product_id', $productId)
            ->where(function ($query) {
                $query
                    ->where('stock', '>', 0)
                    ->orWhereNull('stock');
            })
            ->get();

        $sorted = $productItems->sortBy([
            // letters/numbers/other group
            fn ($a, $b) => (
                (preg_match('/^[A-Za-z]/', $a->name) ? 1 :
                    (preg_match('/^[0-9]/', $a->name) ? 2 : 3))
                    <=>
                    (preg_match('/^[A-Za-z]/', $b->name) ? 1 :
                        (preg_match('/^[0-9]/', $b->name) ? 2 : 3))
            ),

            // if it starts with a number, compare numeric part, else fall back to string
            fn ($a, $b) => (
                (preg_match('/^\D*(\d+)/', $a->name, $ma) ? (int)$ma[1] : PHP_INT_MAX)
                    <=>
                    (preg_match('/^\D*(\d+)/', $b->name, $mb) ? (int)$mb[1] : PHP_INT_MAX)
            ),

            // fallback to full name
            fn ($a, $b) => $a->name <=> $b->name,

            // by price
            fn ($a, $b) => $a->total_price <=> $b->total_price,
        ]);

        $exchangeRate = get_exchange_rate($baseCurrency, $currencyCode);

        return api_status_ok(transformer($sorted, new ProductItemTransformer($exchangeRate)));
    }

    public function showProductItem($id)
    {
        $productItem = ProductItem::with('product')->findOrFail($id);

        return api_status_ok(transformer($productItem, new ProductItemTransformer(), ['product']));
    }

    public function test(OrderService $orderService)
    {
        return $orderService->calculateJokiMLPrice(\request('start_rank'), \request('start_grade'), \request('start_stars'), \request('end_rank'), \request('end_grade'), \request('end_stars'));
    }
}
