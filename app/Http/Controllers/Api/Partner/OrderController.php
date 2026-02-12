<?php

namespace App\Http\Controllers\Api\Partner;

use App\Constants\PlatformConstant;
use App\Events\UserActivityLogged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\OrderRequest;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\Setting;
use App\Services\OrderService;
use App\Transformers\Partner\OrderTransformer;
use App\Transformers\Partner\ProductItemTransformer;
use App\Transformers\ProductTransformer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function show($order)
    {
        $order = Order::with('user', 'productItem.product')->where('code', $order)->first();

        if (empty($order)) {
            return api_status_warning('Order not found');
        }

        if (Auth::id() != null) {
            if ($order->user_id != Auth::id()) {
                return api_status_warning('User id not match!');
            }
        }

        return api_status_ok(transformer($order, new OrderTransformer()));
    }

    public function store(OrderRequest $request, OrderService $orderService)
    {
        try {
            $paymentMethod = PaymentMethod::where('slug', PaymentMethod::BALANCE)->first();

            $request['cust_account'] = json_encode($request->cust_account);
            $request['product_item_id'] = $request->item_code;
            $request['platform'] = PlatformConstant::B2B;
            $request['payment_method_id'] = $paymentMethod->id;
            $request['currency_code'] = Setting::getBaseCurrency();

            $order = $orderService->store($request);

            if (is_array($order)) {
                throw ValidationException::withMessages($order);
            }

            if (is_string($order)) {
                return api_status_warning($order);
            }

            event(new UserActivityLogged(auth()->user()->id, $request->ip(), 'order_created:' . $order->code));

            return api_status_ok(transformer($order, new OrderTransformer()));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return \api_status_error($e);
        }
    }

    public function getProducts()
    {
        $products = Product::select([
            'id',
            'ordering',
            'name',
            'code',
            'input_format',
            'product_category_id',
            'description',
            'company',
            'how_to_order',
            'slug',
            'status',
            // 'provider',
            // 'provider_code',
            'provider_country',
            // 'markup_user',
            // 'markup_reseller_silver',
            // 'markup_reseller_gold',
            // 'markup_reseller_vip',
            'default_picture',
            'default_cover',
            'meta_title',
            'meta_keyword',
            'meta_description',
            // 'deleted_at',
            // 'created_at',
            // 'updated_at',
            'check_uid',
            // 'is_raw_description',
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

    public function getProductItems($productId)
    {
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

        return api_status_ok(transformer($sorted, new ProductItemTransformer()));
    }
}
