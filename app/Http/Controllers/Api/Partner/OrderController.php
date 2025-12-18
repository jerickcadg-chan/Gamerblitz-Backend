<?php

namespace App\Http\Controllers\Api\Partner;

use App\Constants\PlatformConstant;
use App\Events\UserActivityLogged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\OrderRequest;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\ProductItem;
use App\Models\Setting;
use App\Services\OrderService;
use App\Transformers\Partner\OrderTransformer;
use App\Transformers\Partner\ProductItemTransformer;
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
