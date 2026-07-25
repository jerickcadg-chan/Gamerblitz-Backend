<?php

namespace App\Http\Controllers;

use App\Constants\GatewayFeeConstant;
use App\Constants\StatusConst;
use App\Events\UserActivityLogged;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    private string $title;

    public function __construct()
    {
        $this->title = 'Order';

        $this->middleware(['permission:View Order'])->only('index', 'show');
        $this->middleware(['permission:Process Order'])->only('setStatus');
    }

    public function index()
    {
        $orders = Order::latest()
            ->with('productItem', 'user', 'updater', 'paymentMethod', 'affiliate.user', 'affiliateHistory')
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

        // Calculate order statistics - wrapped in try-catch for safety
        $orderStats = $this->getOrderStats();

        return view('orders.index', compact('orders', 'title', 'orderStats'));
    }

    public function show(Order $order)
    {
        $title = $this->title;
        $order->loadMissing('paymentMethod', 'affiliate.user', 'affiliateHistory');

        // Calculate profit breakdown for this order with gateway fees and affiliate bonus
        $vendor     = strtolower($order->paymentMethod?->vendor ?? 'manual');
        $slug       = $order->paymentMethod?->slug ?? $order->paymentMethod?->name ?? '';
        $gatewayFee = ($vendor === 'manual' || $order->turnover <= 0)
            ? 0
            : GatewayFeeConstant::calculateGatewayFee($order->turnover, $vendor, $slug);
        $vatOnFee      = $gatewayFee * 0.12;
        $affiliateBonus = $order->affiliateHistory?->amount ?? 0;
        $netProfit     = $order->total_income - $gatewayFee - $vatOnFee - $affiliateBonus;
        $hasGatewayFee = $gatewayFee > 0;

        $profitBreakdown = [
            'gross_profit' => $order->total_income,
            'gateway_fee' => $gatewayFee,
            'vat_on_fee' => $vatOnFee,
            'affiliate_bonus' => $affiliateBonus,
            'net_profit' => $netProfit,
            'has_gateway_fee' => $hasGatewayFee,
        ];

        // Try to get balance mutations if model exists
        $mutations = collect();
        try {
            $balanceHistoryClass = 'App\\Models\\BalanceHistory';
            if (class_exists($balanceHistoryClass)) {
                $mutations = $balanceHistoryClass::latest()
                    ->where('balanceable_id', $order->id)
                    ->where('balanceable_type', 'App\Models\Order')
                    ->get();
            }
        } catch (\Exception $e) {
            Log::warning('BalanceHistory not available: ' . $e->getMessage());
        }

        return view('orders.show', compact('order', 'mutations', 'title', 'profitBreakdown'));
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
        if (class_exists('App\Events\UserActivityLogged')) {
            event(new UserActivityLogged(auth()->user()->id, request()->ip(), 'order_updated:' . $order->code));
        }

        $orderService->updateStatus($order, $request->status);

        if ($order->paymentMethod->slug == PaymentMethod::BALANCE && in_array($order->status, [StatusConst::FAILED, StatusConst::REFUNDED])) {
            try {
                $balance = \App\Models\Balance::where('user_id', $order->user_id)->first();
                \App\Services\BalanceService::update($balance, [
                    'balanceable_type' => Order::class,
                    'balanceable_id' => $order->id,
                    'amount' => $order->total_price,
                    'description' => "Refund $order->code"
                ]);
            } catch (\Exception $e) {
                Log::error('Balance refund failed: ' . $e->getMessage());
            }
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

        if (class_exists('App\Events\UserActivityLogged')) {
            event(new UserActivityLogged(auth()->user()->id, request()->ip(), 'order_processed:' . $order->code));
        }

        $order->updated_by = auth()->user()->id;
        $order->save();

        $orderService->updateStatus($order, StatusConst::ON_PROCESS);
        $orderService->processOrder($order, sync: true);

        toast('Order is being processed', 'success');
        return redirect()->back();
    }

    // getGatewayFeeRate() removed — use GatewayFeeConstant::calculateGatewayFee() directly

    /**
     * Get order statistics for the index page
     */
    private function getOrderStats(): array
    {
        try {
            $todayStart = Carbon::today()->startOfDay();
            $todayEnd = Carbon::today()->endOfDay();
            $yesterdayStart = Carbon::yesterday()->startOfDay();
            $yesterdayEnd = Carbon::yesterday()->endOfDay();
            $weekStart = Carbon::now()->startOfWeek();
            $weekEnd = Carbon::now()->endOfWeek();
            $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
            $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();
            $monthStart = Carbon::now()->startOfMonth();
            $monthEnd = Carbon::now()->endOfMonth();
            $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
            $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

            $ordersToday = Order::whereBetween('created_at', [$todayStart, $todayEnd])->where('status', 'success')->count();
            $ordersYesterday = Order::whereBetween('created_at', [$yesterdayStart, $yesterdayEnd])->where('status', 'success')->count();
            $ordersThisWeek = Order::whereBetween('created_at', [$weekStart, $weekEnd])->where('status', 'success')->count();
            $ordersLastWeek = Order::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->where('status', 'success')->count();
            $ordersThisMonth = Order::whereBetween('created_at', [$monthStart, $monthEnd])->where('status', 'success')->count();
            $ordersLastMonth = Order::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->where('status', 'success')->count();
            $mustActionOrders = Order::whereIn('status', ['pending', 'on-process', 'delayed'])->count();

            // Most ordered products this month
            $mostOrderedProducts = Order::whereBetween('orders.created_at', [$monthStart, $monthEnd])
                ->where('orders.status', 'success')
                ->join('product_items', 'orders.product_item_id', '=', 'product_items.id')
                ->join('products', 'product_items.product_id', '=', 'products.id')
                ->select('products.id', 'products.name', DB::raw('COUNT(*) as order_count'))
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('order_count')
                ->limit(3)
                ->get();

            // Highest earning products
            $highestEarningProducts = $this->getHighestEarningProducts($monthStart, $monthEnd, 3);

            // Top user by amount
            $topUserMonthByAmount = Order::whereBetween('orders.created_at', [$monthStart, $monthEnd])
                ->where('orders.status', 'success')
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->select('users.id', 'users.name', DB::raw('SUM(orders.turnover) as total_amount'), DB::raw('COUNT(*) as order_count'))
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('total_amount')
                ->first();

            // Top user by orders
            $topUserMonthByOrders = Order::whereBetween('orders.created_at', [$monthStart, $monthEnd])
                ->where('orders.status', 'success')
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->select('users.id', 'users.name', DB::raw('COUNT(*) as order_count'))
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('order_count')
                ->first();

            // Status breakdown
            $orderStatusCounts = Order::whereBetween('created_at', [$monthStart, $monthEnd])
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $allStatuses = config('array.order.status', ['success', 'failed', 'on-process', 'pending', 'delayed', 'expired']);
            $statusBreakdown = [];
            foreach ($allStatuses as $status) {
                $statusBreakdown[$status] = $orderStatusCounts[$status] ?? 0;
            }

            $totalOrdersThisMonth = array_sum($statusBreakdown);
            $successRate = $totalOrdersThisMonth > 0
                ? round(($statusBreakdown['success'] ?? 0) / $totalOrdersThisMonth * 100, 1)
                : 0;

            return [
                'orders_today' => $ordersToday,
                'orders_yesterday' => $ordersYesterday,
                'orders_today_change' => $ordersYesterday > 0 ? round((($ordersToday - $ordersYesterday) / $ordersYesterday) * 100, 1) : ($ordersToday > 0 ? 100 : 0),
                'orders_this_week' => $ordersThisWeek,
                'orders_last_week' => $ordersLastWeek,
                'orders_week_change' => $ordersLastWeek > 0 ? round((($ordersThisWeek - $ordersLastWeek) / $ordersLastWeek) * 100, 1) : ($ordersThisWeek > 0 ? 100 : 0),
                'orders_this_month' => $ordersThisMonth,
                'orders_last_month' => $ordersLastMonth,
                'orders_month_change' => $ordersLastMonth > 0 ? round((($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth) * 100, 1) : ($ordersThisMonth > 0 ? 100 : 0),
                'must_action_orders' => $mustActionOrders,
                'most_ordered_products' => $mostOrderedProducts,
                'highest_earning_products' => $highestEarningProducts,
                'top_user_month_amount' => $topUserMonthByAmount,
                'top_user_month_orders' => $topUserMonthByOrders,
                'success_rate' => $successRate,
                'status_breakdown' => $statusBreakdown,
                'total_orders_this_month' => $totalOrdersThisMonth,
            ];
        } catch (\Exception $e) {
            Log::error('getOrderStats failed: ' . $e->getMessage());
            return [
                'orders_today' => 0, 'orders_yesterday' => 0, 'orders_today_change' => 0,
                'orders_this_week' => 0, 'orders_last_week' => 0, 'orders_week_change' => 0,
                'orders_this_month' => 0, 'orders_last_month' => 0, 'orders_month_change' => 0,
                'must_action_orders' => 0,
                'most_ordered_products' => collect(),
                'highest_earning_products' => [],
                'top_user_month_amount' => null,
                'top_user_month_orders' => null,
                'success_rate' => 0,
                'status_breakdown' => [],
                'total_orders_this_month' => 0,
            ];
        }
    }

    /**
     * Get highest earning products with gateway fee calculations
     */
    private function getHighestEarningProducts($startDate, $endDate, $limit = 3)
    {
        try {
            $orders = Order::whereBetween('orders.created_at', [$startDate, $endDate])
                ->where('orders.status', 'success')
                ->join('product_items', 'orders.product_item_id', '=', 'product_items.id')
                ->join('products', 'product_items.product_id', '=', 'products.id')
                ->leftJoin('payment_methods', 'orders.payment_method_id', '=', 'payment_methods.id')
                ->select(
                    'products.id',
                    'products.name',
                    'orders.total_income',
                    'orders.turnover',
                    'payment_methods.name as payment_method_name',
                    'payment_methods.vendor as payment_method_vendor',
                    'payment_methods.slug as payment_method_slug'
                )
                ->get();

            $productProfits = [];
            $balancePaymentMethods = ['gpds coin', 'gpds_coin', 'balance', 'wallet'];

            foreach ($orders as $order) {
                $productId = $order->id;
                $productName = $order->name;
                $vendor     = strtolower($order->payment_method_vendor ?? 'manual');
                $slug       = $order->payment_method_slug ?? $order->payment_method_name ?? '';

                $gatewayFee = ($vendor === 'manual' || $order->turnover <= 0)
                    ? 0
                    : GatewayFeeConstant::calculateGatewayFee($order->turnover, $vendor, $slug);
                $vatOnFee   = $gatewayFee * 0.12;
                $netProfit  = $order->total_income - $gatewayFee - $vatOnFee;

                if (!isset($productProfits[$productId])) {
                    $productProfits[$productId] = [
                        'id' => $productId,
                        'name' => $productName,
                        'total_profit' => 0,
                    ];
                }
                $productProfits[$productId]['total_profit'] += $netProfit;
            }

            usort($productProfits, function ($a, $b) {
                return $b['total_profit'] <=> $a['total_profit'];
            });

            return array_slice($productProfits, 0, $limit);
        } catch (\Exception $e) {
            Log::error('getHighestEarningProducts failed: ' . $e->getMessage());
            return [];
        }
    }
}
