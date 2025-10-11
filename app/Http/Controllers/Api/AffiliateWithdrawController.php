<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AffiliateWithdraw;
use App\Models\User;
use App\Transformers\AffiliateWithdrawTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AffiliateWithdrawController extends Controller
{
    public string $userId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->userId = auth()->user()->id ?? null;

            return $next($request);
        });
    }

    public function index()
    {
        $affiliateWithdraws = AffiliateWithdraw::latest()->where('user_id', $this->userId);

        return api_status_ok(paginateTransformer($affiliateWithdraws, new AffiliateWithdrawTransformer()));
    }

    public function claim(Request $request)
    {
        try {
            $user = User::findOrFail($this->userId);

            $affiliate = Affiliate::where('user_id', $user->id)->lockForUpdate()->first();

            if ($affiliate->balance < 1) {
                return api_status_warning('Not enough balance');
            }

            $affiliateWithdraw = AffiliateWithdraw::create([
                'affiliate_id' => $affiliate->id,
                'user_id' => $user->id,
                'amount' => $affiliate->balance,
                'requested_at' => now()
            ]);

            $amountBefore = $affiliate->balance;
            $affiliate->balance -= $affiliate->balance;
            $affiliate->save();

            $affiliate->affiliateHistories()->create([
                'affiliate_id'       => $affiliate->id,
                'affiliateable_type' => 'App\Models\AffiliateWithdraw',
                'affiliateable_id'   => $affiliateWithdraw->id,
                'amount'             => $affiliateWithdraw->amount,
                'amount_before'      => $amountBefore,
                'latest_balance'     => $affiliate->balance,
                'description'        => "Request withdraw {$affiliateWithdraw->amount}",
            ]);

            return api_status_ok($affiliateWithdraw);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return api_status_warning("Failed to request withdrawal");
        }
    }
}
