<?php

namespace App\Http\Controllers;

use App\Constants\DefaultRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserIpLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:View User IP Logs']);
    }

    public function index()
    {
        $query = DB::table('user_ip_logs')
            ->leftJoin('users', 'user_ip_logs.user_id', '=', 'users.id')
            ->leftJoin('banned_ips', 'user_ip_logs.ip_address', '=', 'banned_ips.ip_address')
            ->select('user_ip_logs.*', 'users.name', 'users.email', DB::raw('banned_ips.id IS NOT NULL as is_banned'));

        if (request('type') === 'customer') {
            $query->whereNotNull('user_ip_logs.user_id');
            $query->whereExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('model_has_roles')
                  ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                  ->whereRaw('model_has_roles.model_id = users.id')
                  ->whereIn('roles.name', [DefaultRole::CUSTOMER, DefaultRole::RESELLER_SILVER, DefaultRole::RESELLER_GOLD, DefaultRole::RESELLER_VIP]);
            });
        } elseif (request('type') === 'non_customer') {
            $query->whereNotNull('user_ip_logs.user_id');
            $query->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('model_has_roles')
                  ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                  ->whereRaw('model_has_roles.model_id = users.id')
                  ->whereIn('roles.name', [DefaultRole::CUSTOMER, DefaultRole::RESELLER_SILVER, DefaultRole::RESELLER_GOLD, DefaultRole::RESELLER_VIP]);
            });
        } elseif (request('type') === 'guest') {
            $query->whereNull('user_ip_logs.user_id');
        }

        if (request('ip')) {
            $query->where('user_ip_logs.ip_address', 'like', '%' . request('ip') . '%');
        }

        if (request('search')) {
            $query->where(function ($q) {
                $q->where('users.name', 'like', '%' . request('search') . '%')
                  ->orWhere('users.email', 'like', '%' . request('search') . '%');
            });
        }

        $logs = $query->orderBy('user_ip_logs.created_at', 'desc')
            ->limit(50)
            ->get();

        return view('admin.user-ip-logs', compact('logs'));
    }
}