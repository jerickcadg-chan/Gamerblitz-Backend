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
        $month = today()->format('m');
        $year = today()->format('Y');

        $order = Order::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        $progress = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_income) as total_income'),
            DB::raw('count(*) as count')
        )
        ->settlement()
        ->whereMonth('created_at', $month)
        ->whereYear('created_at', $year)
        ->groupBy('date')
        ->get();

        $orderSum = [
            'total' => $order->count(),
            'expired' => $order->where('order_status', Order::EXPIRED)->count(),
            'done' => $order->where('order_status', Order::DONE)->count(),
        ];

        return view('home', \compact('orderSum', 'progress'));
    }
}
