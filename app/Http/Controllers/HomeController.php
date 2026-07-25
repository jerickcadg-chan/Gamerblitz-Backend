<?php

namespace App\Http\Controllers;

use App\Constants\GatewayFeeConstant;
use App\Constants\StatusConst;
use App\Models\Deposit;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function index()
    {
        // --- Filter mode: lifetime | year | month (default: month) ---
        $filterMode    = request('filter_mode', 'month');
        $selectedMonth = request('month', Carbon::now()->month);
        $selectedYear  = request('year', Carbon::now()->year);

        // Determine date range based on filter mode
        switch ($filterMode) {
            case 'lifetime':
                $startDate = Carbon::createFromDate(2000, 1, 1)->startOfDay();
                $endDate   = Carbon::now()->endOfDay();
                $filterLabel = 'Lifetime';
                break;

            case 'year':
                $startDate = Carbon::createFromDate($selectedYear, 1, 1)->startOfYear();
                $endDate   = Carbon::createFromDate($selectedYear, 12, 31)->endOfYear();
                $filterLabel = 'Year ' . $selectedYear;
                break;

            case 'month':
            default:
                $filterMode  = 'month';
                $startDate   = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
                $endDate     = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth();
                $filterLabel = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->format('F Y');
                break;
        }

        $todayStart = Carbon::today()->startOfDay();
        $todayEnd   = Carbon::today()->endOfDay();

        // Orders for selected period
        $orderSum = Order::where('status', StatusConst::SUCCESS)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('COUNT(*) as total, COALESCE(SUM(turnover), 0) as turnover, COALESCE(SUM(total_income), 0) as profit')
            ->first();

        $orderSum['profitMargin'] = $orderSum['turnover'] > 0
            ? round(($orderSum['profit'] / $orderSum['turnover']) * 100, 1)
            : 0;

        // Orders for today
        $orderToday = Order::where('status', StatusConst::SUCCESS)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->selectRaw('COUNT(*) as total, COALESCE(SUM(turnover), 0) as turnover, COALESCE(SUM(total_income), 0) as profit')
            ->first();

        // Period gateway fees — only order fees are deducted from profit
        // Deposit fees are informational only (cost of wallet funding, not order profit)
        $orderGatewayFees   = $this->calculateOrderGatewayFeesAccurate($startDate, $endDate);
        $depositGatewayFees = $this->calculateDepositGatewayFees($startDate, $endDate);
        $vatOnFees          = GatewayFeeConstant::calculateVatOnFee($orderGatewayFees);
        $netProfit          = $orderSum['profit'] - $orderGatewayFees - $vatOnFees;
        $netMargin          = $orderSum['turnover'] > 0
            ? round(($netProfit / $orderSum['turnover']) * 100, 1)
            : 0;

        // Today's gateway fees
        $orderGatewayFeesToday = $this->calculateOrderGatewayFeesAccurate($todayStart, $todayEnd);
        $vatOnFeesToday        = $orderGatewayFeesToday * 0.12;
        $netProfitTodayValue   = $orderToday['profit'] - $orderGatewayFeesToday - $vatOnFeesToday;

        $netProfitStats = [
            'gateway_fees'       => $orderGatewayFees,
            'vat_on_fees'        => $vatOnFees,
            'net_profit'         => $netProfit,
            'net_margin'         => $netMargin,
            'order_gateway_fees' => $orderGatewayFees,
            'deposit_gateway_fees' => $depositGatewayFees,
        ];

        $netProfitToday = [
            'gateway_fees' => $orderGatewayFeesToday,
            'vat_on_fees'  => $vatOnFeesToday,
            'net_profit'   => $netProfitTodayValue,
        ];

        // Past week data (always last 7 days regardless of filter)
        $orderPastWeek = ['days' => [], 'turnover' => [], 'profit' => []];
        for ($i = 6; $i >= 0; $i--) {
            $date      = Carbon::today()->subDays($i);
            $dayOrders = Order::where('status', StatusConst::SUCCESS)
                ->whereDate('created_at', $date)
                ->selectRaw('COALESCE(SUM(turnover), 0) as turnover, COALESCE(SUM(total_income), 0) as profit')
                ->first();
            $orderPastWeek['days'][]     = $date->format('D');
            $orderPastWeek['turnover'][] = (float) $dayOrders->turnover;
            $orderPastWeek['profit'][]   = (float) $dayOrders->profit;
        }

        // Orders by status for selected period
        $ordersByStatus = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Recent orders
        $recentOrders = Order::with('productItem')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Pending orders
        $pendingOrders = Order::where('status', StatusConst::PENDING)->count();

        // User stats
        $userStats = [
            'total'        => User::count(),
            'newThisMonth' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
        ];

        // Product stats
        $productStats = [
            'total'  => Product::count(),
            'active' => Product::where('status', 'active')->count(),
        ];

        // GPDS Reseller Balance
        $gpdsBalance = $this->fetchGpdsBalance();

        return view('home', compact(
            'filterMode',
            'filterLabel',
            'selectedMonth',
            'selectedYear',
            'orderSum',
            'orderToday',
            'orderPastWeek',
            'ordersByStatus',
            'recentOrders',
            'pendingOrders',
            'userStats',
            'productStats',
            'netProfitStats',
            'netProfitToday',
            'gpdsBalance'
        ));
    }

    /**
     * Fetch the reseller's GPDS wallet balance via the whitelabel API
     */
    private function fetchGpdsBalance(): array
    {
        try {
            $baseUrl      = Setting::getByKey('whitelabel_api_url');
            $token        = Setting::getByKey('whitelabel_api_token');
            $currencyCode = strtoupper(env('PROVIDER_CURRENCY', 'PHP'));

            if (empty($baseUrl) || empty($token)) {
                return ['balance' => 0, 'currency' => $currencyCode, 'error' => 'GPDS API not configured'];
            }

            $response = Http::timeout(5)
                ->withHeaders(['Authorization' => $token])
                ->get("{$baseUrl}/partner/balance", ['currency_code' => $currencyCode]);

            if ($response->successful()) {
                $data    = $response->json();
                $balance = $data['payload'] ?? $data['data'] ?? $data['balance'] ?? 0;
                if (is_array($balance)) {
                    $balance = 0;
                }
                return ['balance' => (float) $balance, 'currency' => $currencyCode, 'error' => null];
            }

            return ['balance' => 0, 'currency' => $currencyCode, 'error' => 'API returned: ' . $response->status()];
        } catch (\Exception $e) {
            return ['balance' => 0, 'currency' => env('PROVIDER_CURRENCY', 'PHP'), 'error' => $e->getMessage()];
        }
    }

    /**
     * Calculate order gateway fees using GatewayFeeConstant (vendor + slug based)
     */
    private function calculateOrderGatewayFeesAccurate($startDate, $endDate): float
    {
        $orders = Order::where('status', StatusConst::SUCCESS)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('paymentMethod')
            ->get();

        $totalFees = 0;
        foreach ($orders as $order) {
            if (!$order->paymentMethod || $order->turnover <= 0) {
                continue;
            }
            $vendor = strtolower($order->paymentMethod->vendor ?? '');
            // Manual / GamerBlitz Coin payments have zero gateway fee
            if ($vendor === 'manual') {
                continue;
            }
            $slug       = $order->paymentMethod->slug ?? $order->paymentMethod->name ?? '';
            $totalFees += GatewayFeeConstant::calculateGatewayFee($order->turnover, $vendor, $slug);
        }

        return round($totalFees, 2);
    }

    /**
     * Calculate gateway fees for deposits
     */
    private function calculateDepositGatewayFees($startDate, $endDate): float
    {
        $deposits = Deposit::where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('paymentMethod')
            ->select('total_amount', 'payment_method_id')
            ->get();

        $totalFees = 0;
        foreach ($deposits as $deposit) {
            if ($deposit->paymentMethod) {
                $vendor     = $deposit->paymentMethod->vendor ?? 'xendit';
                $slug       = $deposit->paymentMethod->slug ?? $deposit->paymentMethod->name ?? '';
                $totalFees += GatewayFeeConstant::calculateGatewayFee($deposit->total_amount, $vendor, $slug);
            }
        }

        return round($totalFees, 2);
    }
}
