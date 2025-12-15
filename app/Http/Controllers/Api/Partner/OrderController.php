<?php

namespace App\Http\Controllers\Api\Partner;

use App\Events\UserActivityLogged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\OrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Transformers\Partner\OrderTransformer;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user', 'productItem.product')->latest()
            ->where('user_id', auth()->id)
            ->when(request('order_code'), function ($query) {
                return $query->where('code', 'like', '%' . request('order_code') . '%');
            });

        return api_status_ok(paginateTransformer($orders, new OrderTransformer(), [], \request('limit') ?? 10));
    }

    public function show($order)
    {
        $order = Order::with('user', 'productItem.product')->where('code', $order)->first();

        if (empty($order)) {
            return api_status_warning('Order not found');
        }

        if (auth()->id != null) {
            if ($order->user_id != auth()->id) {
                return api_status_warning('User id not match!');
            }
        }

        return api_status_ok(transformer($order, new OrderTransformer()));
    }

    public function store(OrderRequest $request, OrderService $orderService)
    {
        try {
            $order = $orderService->store($request->fieldInputs());

            if (is_array($order)) {
                throw ValidationException::withMessages($order);
            }

            if (is_string($order)) {
                return api_status_warning($order);
            }

            event(new UserActivityLogged(auth()->id, $request->ip(), 'order_created:' . $order->code));

            return api_status_ok(transformer($order, new OrderTransformer()));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return \api_status_error($e);
        }
    }
}
