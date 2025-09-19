<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AffiliateWithdraw;
use App\Models\User;
use App\Transformers\AffiliateWithdrawTransformer;
use Illuminate\Http\Request;

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

        $affiliate->balance -= $affiliate->balance;
        $affiliate->save();

        return api_status_ok($affiliateWithdraw);
    }
}
