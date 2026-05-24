<?php

namespace App\Http\Controllers;

use App\Events\UserActivityLogged;
use App\Models\Balance;
use App\Services\DepositService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Deposit;

class DepositController extends Controller
{
    private string $title;

    public function __construct()
    {
        $this->title = 'Deposit';

        $this->middleware(['permission:View Deposit'])->only('index', 'show');
        $this->middleware(['permission:Edit Deposit'])->only('updateStatus');
    }

    public function index()
    {
        $deposits = Deposit::with('user', 'updater')
            ->latest()
            ->whereHas('user', function (Builder $query) {
                $query
                    ->when(request('name'), function (Builder $query) {
                        $query->where('name', 'like', '%' . \request('name') . '%');
                    });
            })
            ->when(\request('code'), function (Builder $query) {
                $query->where('code', 'like', '%' . \request('code') . '%');
            })
            ->paginate();

        $title = $this->title;

        // Calculate deposit statistics
        $depositStats = $this->getDepositStats();

        return view('deposits.index', compact('deposits', 'title', 'depositStats'));
    }

    public function show(Deposit $deposit)
    {
        $title = $this->title;

        return view('deposits.show', compact('deposit', 'title'));
    }

    public function updateStatus(Deposit $deposit, Request $request)
    {
        try {
            $deposit->updated_by = auth()->user()->id;
            $deposit->save();

            // Log user action
            event(new UserActivityLogged(auth()->user()->id, request()->ip(), 'deposit_updated:' . $deposit->code));

            $action = DepositService::updateStatus($deposit, $request->status, $request->amount);

            if (!$action['status']) {
                toast($action['message'] ?? "Failed", 'error');
                return redirect()->back();
            }

            toast("Deposit status updated", 'success');
            return redirect()->route('deposit.index');
        } catch (\Exception $e) {
            toast("Deposit status failed", 'error');
            return redirect()->route('deposit.index');
        }
    }

    /**
     * Get deposit statistics for the index page
     */
    private function getDepositStats(): array
    {
        // Today
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();
        
        // Yesterday
        $yesterdayStart = Carbon::yesterday()->startOfDay();
        $yesterdayEnd = Carbon::yesterday()->endOfDay();
        
        // This week
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        
        // Last week
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();
        
        // This month
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        
        // Last month
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Current period values
        $today = Deposit::where('status', 'paid')
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->sum('total_amount');
            
        $yesterday = Deposit::where('status', 'paid')
            ->whereBetween('created_at', [$yesterdayStart, $yesterdayEnd])
            ->sum('total_amount');
            
        $thisWeek = Deposit::where('status', 'paid')
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->sum('total_amount');
            
        $lastWeek = Deposit::where('status', 'paid')
            ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
            ->sum('total_amount');
            
        $thisMonth = Deposit::where('status', 'paid')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('total_amount');
            
        $lastMonth = Deposit::where('status', 'paid')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('total_amount');

        return [
            'today' => $today,
            'yesterday' => $yesterday,
            'today_change' => $yesterday > 0 ? round((($today - $yesterday) / $yesterday) * 100, 2) : ($today > 0 ? 100 : 0),
            
            'this_week' => $thisWeek,
            'last_week' => $lastWeek,
            'week_change' => $lastWeek > 0 ? round((($thisWeek - $lastWeek) / $lastWeek) * 100, 2) : ($thisWeek > 0 ? 100 : 0),
            
            'this_month' => $thisMonth,
            'last_month' => $lastMonth,
            'month_change' => $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2) : ($thisMonth > 0 ? 100 : 0),
            
            'total_balance' => Balance::where('amount', '>', 0)->sum('amount'),
        ];
    }
}