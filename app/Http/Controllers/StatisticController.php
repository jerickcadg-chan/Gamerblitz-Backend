<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;

class StatisticController extends Controller
{
    public function showOrderStatistic()
    {
        $startDate = request('startDate') ?? now()->subWeek()->format('Y-m-d');
        $endDate = request('endDate') ?? now()->format('Y-m-d');
        $query = Order::whereClient()
            ->where('order_status', Order::DONE)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total_price) as total_price, SUM(total_income) as total_income')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date');

        $count = $query->pluck('count');
        $turnover = $query->pluck('total_price')->map(
            function ($total) {
                return round($total);
            }
        );
        $profit = $query->pluck('total_income')->map(
            function ($total) {
                return round($total);
            }
        );
        $days = $query->pluck('date');

        $data = compact('count', 'turnover', 'profit', 'days', 'startDate', 'endDate');
        return view('statistics.order', $data);
    }

    public function showUserStatistic()
    {
        $startDate = request('startDate') ?? now()->subWeek()->format('Y-m-d');
        $endDate = request('endDate') ?? now()->format('Y-m-d');
        $query = User::whereClient()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date');

        $count = $query->pluck('count');
        $days = $query->pluck('date');

        $data = compact('count', 'days', 'startDate', 'endDate');
        return view('statistics.user', $data);
    }
}
