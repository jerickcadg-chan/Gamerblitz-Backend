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
                ROUND(SUM(converted_price)) as turnover,
                ROUND(SUM(converted_total_income)) as profit,
                ROUND((SUM(converted_total_income) / NULLIF(SUM(converted_price), 0)) * 100) as profit_margin
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
}
