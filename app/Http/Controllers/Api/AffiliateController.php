<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AffiliateHistory;
use App\Transformers\AffiliateHistoryTransformer;

class AffiliateController extends Controller
{
    public string $userId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->userId = auth()->user()->id ?? null;

            return $next($request);
        });
    }

    public function balanceLog()
    {
        $affiliate = Affiliate::where('user_id', $this->userId)->firstOrFail();
        if (!$affiliate) {
            return api_status_warning('Not affiliate');
        }

        $affiliateWithdraws = AffiliateHistory::latest()->where('affiliate_id', $affiliate->id);

        return api_status_ok(paginateTransformer($affiliateWithdraws, new AffiliateHistoryTransformer()));
    }
}
