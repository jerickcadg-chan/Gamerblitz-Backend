<?php

namespace App\Http\Controllers;

use App\Constants\StatusConst;
use App\Events\UserActivityLogged;
use App\Mail\SendOrderNotif;
use App\Models\Balance;
use App\Models\BalanceHistory;
use App\Models\PaymentMethod;
use App\Services\BalanceService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\DynastyDgsService;
use App\Services\OrderService;

class OrderController extends Controller
{
    private string $title;

    public function __construct()
    {
        $this->title = 'Order';

        $this->middleware(['permission:View Order'])->only('index', 'show');
    }

    public function index()
    {
        $orders = Order::latest()
            ->with('productItem', 'user', 'updater')
            ->when(request('cust_account'), function ($query) {
                return $query->where('cust_account', 'like', '%'. request('cust_account') .'%');
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
            ->when(request('status'), function ($query) {
                return $query->where('status', request('status'));
            })
            ->when(request('dates'), function (Builder $q) {
                $range = get_start_and_end_date_with_hours(request('dates'));
                return $q->whereBetween('created_at', [$range['start_date'], $range['end_date']]);
            })
            ->paginate();

        $title = $this->title;

        return view('orders.index', compact('orders', 'title'));
    }

    public function show(Order $order)
    {
        $gdsService = new DynastyDgsService();

        $title = $this->title;

        $mutations = BalanceHistory::latest()
            ->where('balanceable_id', $order->id)
            ->where('balanceable_type', 'App\Models\Order')
            ->get();

        return view('orders.show', compact('order', 'mutations', 'title'));
    }

    public function setStatus(Request $request, OrderService $orderService)
    {
        $order = Order::findOrFail($request->order_id);

        $order->update(array_filter(
            $request->only(['serial_number', 'note']),
            fn ($value) => $value !== null && $value !== ''
        ));

        $order->updated_by = auth()->user()->id;
        $order->save();

        // Log user action
        event(new UserActivityLogged(auth()->user()->id, request()->ip(), 'order_updated:' . $order->code));

        $orderService->updateStatus($order, $request->status);

        if ($order->paymentMethod->slug == PaymentMethod::BALANCE && in_array($order->status, [StatusConst::FAILED, StatusConst::REFUNDED])) {
            $balance = Balance::where('user_id', $order->user_id)->first();

            BalanceService::update($balance, [
                'balanceable_type' => Order::class,
                'balanceable_id' => $order->id,
                'amount' => $order->total_price,
                'description' => "Refund $order->code"
            ]);
        }

        toast('Changed order status to '. $order->status, 'success');
        return redirect()->back();
    }

    public function triggerProcessOrder(Request $request, OrderService $orderService)
    {
        $order = Order::findOrFail($request->order_id);

        if ($order->status !== StatusConst::DELAY && $order->status !== StatusConst::PENDING) {
            toast('Only pending or delayed order can be processed', 'warning');
            return redirect()->back();
        }

        // Log user action
        event(new UserActivityLogged(auth()->user()->id, request()->ip(), 'order_processed:' . $order->code));

        $order->updated_by = auth()->user()->id;
        $order->save();

        $orderService->updateStatus($order, StatusConst::ON_PROCESS);
        $orderService->processOrder($order, sync: true);

        toast('Order is being processed', 'success');
        return redirect()->back();
    }
}
