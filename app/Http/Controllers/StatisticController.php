<?php

namespace App\Http\Controllers;

use App\Constants\StatusConst;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;

class StatisticController extends Controller
{
    public function showOrderStatistic()
    {
        $daterange = request('daterange');
        if (!$daterange) {
            $startDate = now()->subWeek();
            $endDate = now();
        } else {
            $split = explode(' - ', $daterange);
            $startDate = Carbon::parse($split[0]);
            $endDate = Carbon::parse($split[1]);
        }

        if ($startDate->diffInDays($endDate) > 31) {
            session()->flash('error', 'Date range max 31 dat');
            return redirect()->back();
        }

        $query = Order::where('status', StatusConst::SUCCESS)
            ->selectRaw(
                '
                DATE(created_at) as date,
                COUNT(*) as count,
                SUM(turnover) as turnover,
                SUM(total_income) as profit,
                ROUND((SUM(total_income) / NULLIF(SUM(turnover), 0)) * 100) as profit_margin
                '
            )
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy('date')
            ->orderBy('date');

        $orders = $query->get();

        $data = compact('orders', 'startDate', 'endDate');
        return view('statistics.order', $data);
    }

    public function showUserStatistic()
    {
        $daterange = request('daterange');
        if (!$daterange) {
            $startDate = now()->subWeek();
            $endDate = now();
        } else {
            $split = explode(' - ', $daterange);
            $startDate = Carbon::parse($split[0]);
            $endDate = Carbon::parse($split[1]);
        }

        if ($startDate->diffInDays($endDate) > 31) {
            session()->flash('error', 'Date range max 31 day');
            return redirect()->back();
        }

        $query = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy('date')
            ->orderBy('date');

        $count = $query->pluck('count');
        $days = $query->pluck('date');

        $data = compact('count', 'days', 'startDate', 'endDate');
        return view('statistics.user', $data);
    }

    public function showProductStatistic()
    {
        $daterange = request('daterange');

        if (!$daterange) {
            $startDate = now()->subWeek();
            $endDate = now();
        } else {
            $split = explode(' - ', $daterange);
            $startDate = Carbon::parse($split[0]);
            $endDate = Carbon::parse($split[1]);
        }

        if ($startDate->diffInDays($endDate) > 31) {
            session()->flash('error', 'Date range max 31 day');
            return redirect()->back();
        }

        $query = Order::where('orders.status', StatusConst::SUCCESS)
            ->join('product_items', 'orders.product_item_id', '=', 'product_items.id')
            ->join('products', 'product_items.product_id', '=', 'products.id')
            ->selectRaw('
                products.name as product_name,
                COUNT(*) as count,
                SUM(orders.turnover) as turnover,
                SUM(orders.total_income) as profit,
                ROUND((SUM(orders.total_income) / NULLIF(SUM(orders.turnover), 0)) * 100) as profit_margin
            ')
            ->whereBetween('orders.created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('turnover');

        $orders = $query->get();

        return view('statistics.product', compact('orders', 'startDate', 'endDate'));
    }
}
