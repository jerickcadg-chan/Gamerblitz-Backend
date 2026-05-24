<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResellerStatsController extends Controller
{
    /**
     * Return this reseller site's stats to the main GPDS platform.
     * Secured via callback_token in the Authorization header.
     */
    public function stats(Request $request)
    {
        // Authenticate via callback token
        $providedToken = $request->header('Authorization');
        $expectedToken = Setting::getByKey('whitelabel_callback_token');

        if (empty($providedToken) || empty($expectedToken) || $providedToken !== $expectedToken) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $now = Carbon::now();
            $monthStart = $now->copy()->startOfMonth();
            $monthEnd = $now->copy()->endOfMonth();
            $todayStart = $now->copy()->startOfDay();
            $todayEnd = $now->copy()->endOfDay();

            // Total stats (all time)
            $totalOrders = Order::where('status', 'success')->count();
            $totalTurnover = Order::where('status', 'success')->sum('turnover');
            $totalGrossProfit = Order::where('status', 'success')->sum('total_income');
            $totalUsers = User::whereHas('roles', function ($q) {
                $q->where('name', 'Customer');
            })->count();
            $totalProducts = Product::where('status', 'active')->count();

            // This month stats
            $monthOrders = Order::where('status', 'success')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();
            $monthTurnover = Order::where('status', 'success')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('turnover');
            $monthGrossProfit = Order::where('status', 'success')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_income');

            // Today stats
            $todayOrders = Order::where('status', 'success')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->count();
            $todayTurnover = Order::where('status', 'success')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->sum('turnover');
            $todayGrossProfit = Order::where('status', 'success')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->sum('total_income');

            // Pending orders
            $pendingOrders = Order::where('status', 'pending')->count();

            // Net profit estimation (gross - estimated gateway fees & VAT)
            $estimatedFeeRate = 0.023;
            $netProfit = $totalGrossProfit - ($totalTurnover * $estimatedFeeRate) - ($totalTurnover * $estimatedFeeRate * 0.12);
            $monthNetProfit = $monthGrossProfit - ($monthTurnover * $estimatedFeeRate) - ($monthTurnover * $estimatedFeeRate * 0.12);

            return response()->json([
                'method' => 'GET',
                'code' => 200,
                'message' => 'Stats retrieved successfully',
                'payload' => [
                    'site_name' => Setting::getByKey('brand_name') ?? 'Reseller Site',
                    'total_orders' => $totalOrders,
                    'total_turnover' => round($totalTurnover, 2),
                    'total_gross_profit' => round($totalGrossProfit, 2),
                    'net_profit' => round($netProfit, 2),
                    'total_users' => $totalUsers,
                    'total_products' => $totalProducts,
                    'pending_orders' => $pendingOrders,
                    'this_month' => [
                        'orders' => $monthOrders,
                        'turnover' => round($monthTurnover, 2),
                        'gross_profit' => round($monthGrossProfit, 2),
                        'net_profit' => round($monthNetProfit, 2),
                    ],
                    'today' => [
                        'orders' => $todayOrders,
                        'turnover' => round($todayTurnover, 2),
                        'gross_profit' => round($todayGrossProfit, 2),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching stats',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
