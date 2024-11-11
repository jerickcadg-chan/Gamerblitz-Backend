<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Exports\OrderExport;
use App\Exports\UserExport;

class ReportController extends Controller
{
    private $title;

    public function __construct()
    {
        $this->title = 'Laporan';

        $this->middleware(['permission:View Transaction Report'])->only('getOrder');
        $this->middleware(['permission:View User Report'])->only('getUser');
    }

    public function index()
    {
        $title = $this->title;

        return view('reports.index', compact('title'));
    }

    public function getOrder()
    {
        $orders = Order::latest()
            ->whereDate('created_at', '>=', request('order_start_date'))
            ->whereDate('created_at', '<=', request('order_end_date'))
            ->get();

        $data = new OrderExport($orders);

        return \Excel::download($data, 'laporan_order_'. request('order_start_date') .'_'. request('order_end_date') .'.xls');
    }

    public function getUser()
    {
        $users = User::latest()->customer()
            ->whereDate('created_at', '>=', request('user_start_date'))
            ->whereDate('created_at', '<=', request('user_end_date'))
            ->get();

        $data = new UserExport($users);

        return \Excel::download($data, 'laporan_user_'. request('user_start_date') .'_'. request('user_end_date') .'.xls');
    }
}
