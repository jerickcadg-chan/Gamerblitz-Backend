<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AgentRankingController extends Controller
{
    public function index()
    {
        $rankings = DB::table('affiliates as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->leftJoin('affiliate_histories as ah', function ($join) {
                $join->on('ah.affiliate_id', '=', 'a.id')
                     ->where('ah.affiliateable_type', '=', 'App\\Models\\Order');
            })
            ->selectRaw('
                a.id AS affiliate_id,
                u.name AS affiliate_name,
                u.email AS affiliate_email,
                COALESCE(SUM(ah.amount), 0) AS net_total,
                a.balance AS current_balance
            ')
            ->groupBy('a.id', 'u.name', 'u.email', 'a.balance')
            ->havingRaw('net_total > 0')
            ->orderByDesc('net_total')
            ->limit(50)
            ->get();

        return response()->json([
            'status' => 'ok',
            'data' => $rankings,
        ]);
    }
}
