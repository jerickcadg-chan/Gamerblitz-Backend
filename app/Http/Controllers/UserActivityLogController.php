<?php

namespace App\Http\Controllers;

use App\Constants\DefaultRole;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:View User Activity Logs']);
    }

    public function index()
    {
        $query = DB::table('user_activity_logs')
            ->leftJoin('users', 'user_activity_logs.user_id', '=', 'users.id')
            ->leftJoin('banned_ips', 'user_activity_logs.ip_address', '=', 'banned_ips.ip_address')
            ->select('user_activity_logs.*', 'users.name', 'users.email', DB::raw('banned_ips.id IS NOT NULL as is_banned'));

        if (request('type') === 'customer') {
            $query->whereNotNull('user_activity_logs.user_id');
            $query->whereExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('model_has_roles')
                  ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                  ->whereRaw('model_has_roles.model_id = users.id')
                  ->whereIn('roles.name', [DefaultRole::CUSTOMER, DefaultRole::RESELLER_SILVER, DefaultRole::RESELLER_GOLD, DefaultRole::RESELLER_VIP]);
            });
        } elseif (request('type') === 'non_customer') {
            $query->whereNotNull('user_activity_logs.user_id');
            $query->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('model_has_roles')
                  ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                  ->whereRaw('model_has_roles.model_id = users.id')
                  ->whereIn('roles.name', [DefaultRole::CUSTOMER, DefaultRole::RESELLER_SILVER, DefaultRole::RESELLER_GOLD, DefaultRole::RESELLER_VIP]);
            });
        } elseif (request('type') === 'guest') {
            $query->whereNull('user_activity_logs.user_id');
        }

        if (request('ip')) {
            $query->where('user_activity_logs.ip_address', 'like', '%' . request('ip') . '%');
        }

        if (request('search')) {
            $query->where(function ($q) {
                $q->where('users.name', 'like', '%' . request('search') . '%')
                  ->orWhere('users.email', 'like', '%' . request('search') . '%');
            });
        }

        if (request('dates')) {
            $dates = explode(' - ', request('dates'));
            if (count($dates) == 2) {
                $start = Carbon::createFromFormat('m/d/Y', $dates[0])->startOfDay();
                $end = Carbon::createFromFormat('m/d/Y', $dates[1])->endOfDay();
                $query->whereBetween('user_activity_logs.created_at', [$start, $end]);
            }
        }

        $logs = $query->orderBy('user_activity_logs.created_at', 'desc')
            ->paginate(50);

        return view('admin.user-activity-logs', compact('logs'));
    }
}
