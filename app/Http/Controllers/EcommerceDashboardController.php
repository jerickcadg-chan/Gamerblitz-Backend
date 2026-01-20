<?php

namespace App\Http\Controllers;

use App\Models\EcommerceOrder;
use App\Models\EcommerceProduct;
use App\Models\EcommerceCategory;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class EcommerceDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:View Ecommerce Order']);
    }

    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // Get ecommerce order stats (only paid orders: processing, shipped, delivered)
        $paidStatuses = ['processing', 'shipped', 'delivered'];
        
        $ordersQuery = EcommerceOrder::whereIn('status', $paidStatuses)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year);

        $ordersCount = (clone $ordersQuery)->count();
        $turnover = (clone $ordersQuery)->sum('total');

        // Today's stats
        $todayOrdersCount = EcommerceOrder::whereIn('status', $paidStatuses)
            ->whereDate('created_at', today())
            ->count();
        $todayTurnover = EcommerceOrder::whereIn('status', $paidStatuses)
            ->whereDate('created_at', today())
            ->sum('total');

        // Monthly profit (delivered orders only)
        $profitOrders = EcommerceOrder::where('status', 'delivered')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->with('items')
            ->get();
        
        $profit = $profitOrders->sum(function($order) {
            return $order->items->sum(function($item) {
                return ($item->price - ($item->capital_price ?? 0)) * $item->quantity;
            });
        });
        
        $profitPercentage = $turnover > 0 ? round(($profit / $turnover) * 100, 1) : 0;

        // Today's profit (delivered orders only)
        $todayProfitOrders = EcommerceOrder::where('status', 'delivered')
            ->whereDate('created_at', today())
            ->with('items')
            ->get();
        
        $todayProfit = $todayProfitOrders->sum(function($order) {
            return $order->items->sum(function($item) {
                return ($item->price - ($item->capital_price ?? 0)) * $item->quantity;
            });
        });

        // Weekly chart data
        $weeklyData = EcommerceOrder::whereIn('status', $paidStatuses)
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Products and categories count
        $productsCount = EcommerceProduct::where('is_active', true)->count();
        $categoriesCount = EcommerceCategory::where('is_active', true)->count();

        // Pending orders count
        $pendingOrdersCount = EcommerceOrder::where('status', 'pending')->count();
        $processingOrdersCount = EcommerceOrder::where('status', 'processing')->count();

        // Maintenance status
        $maintenanceMode = Setting::where('key', 'ecommerce_shop_maintenance')->value('value') === '1';

        $activePage = 'ecommerce_dashboard';

        return view('ecommerce.dashboard', compact(
            'activePage',
            'ordersCount',
            'turnover',
            'profit',
            'profitPercentage',
            'todayOrdersCount',
            'todayTurnover',
            'todayProfit',
            'weeklyData',
            'productsCount',
            'categoriesCount',
            'pendingOrdersCount',
            'processingOrdersCount',
            'maintenanceMode',
            'month',
            'year'
        ));
    }

    public function toggleMaintenance(Request $request)
    {
        $setting = Setting::where('key', 'ecommerce_shop_maintenance')->first();
        
        if (!$setting) {
            Setting::create([
                'key' => 'ecommerce_shop_maintenance',
                'value' => '1'
            ]);
        } else {
            $setting->update([
                'value' => $setting->value === '1' ? '0' : '1'
            ]);
        }

        return redirect()->route('ecommerce.dashboard')
            ->with('success', 'Maintenance mode updated successfully.');
    }
}
