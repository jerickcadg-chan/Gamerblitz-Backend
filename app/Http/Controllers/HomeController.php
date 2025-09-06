<?php

namespace App\Http\Controllers;

use App\Constants\StatusConst;
use App\Models\Order;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return View
     */
    public function index(): View
    {
        $selectedMonth = request('month') ?? today()->format('m');
        $selectedYear = request('year') ?? today()->format('Y');

        $orderToday = $this->getOrderToday();
        $orderSum = $this->getOrderSum($selectedMonth, $selectedYear);
        $orderPastWeek = $this->getOrderPastWeek();

        $data = compact('orderSum', 'orderToday', 'orderPastWeek', 'selectedYear', 'selectedMonth');

        return view('home', $data);
    }

    protected function getOrderPastWeek()
    {
        $query = Order::where('status', StatusConst::SUCCESS)
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as total_price, SUM(total_income) as total_income')
            ->whereBetween('created_at', [now()->subWeek()->startOfDay(), now()->endOfDay()])
            ->groupBy('date')
            ->orderBy('date');
        $turnover = $query->pluck('total_price')->map(
            function ($total) {
                return round($total);
            }
        );
        $profit = $query->pluck('total_income')->map(
            function($total) {
                return round($total);
            }
        );
        $days = $query->pluck('date');
        return compact('turnover', 'profit', 'days');
    }

    protected function getOrderSum($month, $year)
    {
        $orderSumQuery = Order::query()
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', StatusConst::SUCCESS);

        $turnover = $orderSumQuery->sum('total_price');
        $profit = $orderSumQuery->sum('total_income');
        $profitMargin = $turnover === 0 ? 0 : round(($profit / $turnover) * 100);
        $orderSum = [
            'total' => $orderSumQuery->count(),
            'turnover' => $turnover,
            'profit' => $profit,
            'profitMargin' => $profitMargin,
        ];

        return $orderSum;
    }
    /**
     * @return array<string,mixed>
     */
    protected function getOrderToday()
    {
        $orderTodayQuery = Order::query()
            ->whereRaw('DATE(created_at) = ?', [today()])
            ->where('status', StatusConst::SUCCESS);


        $turnoverToday = $orderTodayQuery->sum('total_price');
        $profitToday = $orderTodayQuery->sum('total_income');
        $orderToday = [
            'total' => $orderTodayQuery->count(),
            'turnover' => $turnoverToday,
            'profit' => $profitToday,
        ];
        return $orderToday;
    }
}
