<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

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
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $selectedMonth = request('month') ?? today()->format('m');
        $selectedYear = request('year') ?? today()->format('Y');

        $order = Order::whereClient()
            ->whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $selectedYear)
            ->where('order_status', Order::DONE);

        // $progress = Order::whereClient()
        //     ->select(
        //         DB::raw('DATE(created_at) as date'),
        //         DB::raw('SUM(total_income) as total_income'),
        //         DB::raw('count(*) as count')
        //     )
        //     ->settlement()
        //     ->whereMonth('created_at', $month)
        //     ->whereYear('created_at', $year)
        //     ->groupBy('date')
        //     ->get();

        $turnover = $order->sum('total_price');
        $profit = $order->sum('total_income');
        $profitPercent = $turnover === 0 ? 0 : round(($profit / $turnover) * 100);
        $orderSum = [
            'total' => $order->count(),
            'turnover' => $turnover,
            'profit' => $profit,
            'profitPercent' => $profitPercent,
        ];

        $data = compact('orderSum', 'selectedYear', 'selectedMonth');
        return view('home', $data);
    }
}
