<?php

namespace App\Http\Controllers;

use App\Mail\SendOrderNotif;
use App\Models\Balance;
use App\Models\PaymentMethod;
use App\Services\BalanceService;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\OrderService;

class OrderController extends Controller
{
    private $title;

    public function __construct()
    {
        $this->title = 'Pesanan';

        $this->middleware(['permission:View Order'])->only('index', 'show');
    }

    public function index()
    {
        $orders = Order::latest()
            ->with('productItem', 'user')
            ->when(request('status'), function ($query) {
                return $query->where('order_status', request('status'));
            })
            ->when(request('order_code'), function ($query) {
                return $query->where('code', 'like', '%'. request('order_code') .'%');
            })
            ->when(request('customer_name'), function ($query) {
                return $query->whereHas('user', function ($query) {
                    return $query->where('name', 'like', '%'. request('customer_name') .'%')
                      ->orWhere('phone_number', 'like', '%'. request('customer_name') .'%')
                      ->orWhere('email', 'like', '%'. request('customer_name') .'%');
                });
            })
            ->when(request('product_id'), function ($query) {
                return $query->whereHas('productItem', function ($query) {
                    return $query->where('product_id', request('product_id'));
                });
            })
            ->when(request('payment_status'), function ($query) {
                return $query->where('payment_status', request('payment_status'));
            })
            ->when(request('date'), function ($query) {
                return $query->whereDate('created_at', request('date'));
            })
            ->paginate();

        $title = $this->title;

        return view('orders.index', compact('orders', 'title'));
    }

    public function show(Order $order)
    {
        $title = $this->title;

        return view('orders.show', compact('order', 'title'));
    }

    public function setStatus(Request $request, OrderService $orderService)
    {
        $order = Order::findOrFail($request->order_id);

        $orderService->updateStatus($order, null, $request->status);

        if ($request->status === Order::CANCELED) {
            $orderService->updateStatus($order, Order::REFUNDED, null);
        }

        if ($order->payment_method == PaymentMethod::SALDO) {
            $balance = Balance::where('user_id', $order->user_id)->first();

            BalanceService::update($balance, [
                'balanceable_type' => Order::class,
                'balanceable_id' => $order->id,
                'amount' => $order->total_price,
                'description' => "Refund $order->code"
            ]);
        }

        if ($order->cust_email) {
            // \Mail::to($order->cust_email)->queue(new SendOrderNotif($order));
        }

        toast('Sukses update status order menjadi '. $order->order_status_translated, 'success');
        return redirect()->back();
    }
}
