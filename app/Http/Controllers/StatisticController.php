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
            ->selectRaw(
                '
                DATE(created_at) as date,
                COUNT(*) as count,
                ROUND(SUM(total_price)) as turnover,
                ROUND(SUM(total_income)) as profit,
                ROUND((SUM(total_income) / NULLIF(SUM(total_price), 0)) * 100) as profit_margin
                '
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date');

        $orders = $query->get();

        $data = compact('orders', 'startDate', 'endDate');
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
