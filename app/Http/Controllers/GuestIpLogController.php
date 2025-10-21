<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuestIpLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:View Guest IP Logs']);
    }

    public function index()
    {
        $query = DB::table('user_ip_logs')
            ->leftJoin('banned_ips', 'user_ip_logs.ip_address', '=', 'banned_ips.ip_address')
            ->whereNull('user_ip_logs.user_id')
            ->select('user_ip_logs.ip_address', 'user_ip_logs.action', 'user_ip_logs.created_at', DB::raw('banned_ips.id IS NOT NULL as is_banned'));

        if (request('ip')) {
            $query->where('user_ip_logs.ip_address', 'like', '%' . request('ip') . '%');
        }

        $logs = $query->orderBy('user_ip_logs.created_at', 'desc')
            ->limit(50)
            ->get();

        return view('admin.guest-ip-logs', compact('logs'));
    }
}