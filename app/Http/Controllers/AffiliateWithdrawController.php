<?php

namespace App\Http\Controllers;

use App\Constants\StatusConst;
use App\Models\Affiliate;
use App\Models\AffiliateWithdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AffiliateWithdrawController extends Controller
{
    private string $title;

    public function __construct()
    {
        $this->title = 'Affiliate Withdraw';

        $this->middleware(['permission:View Withdraw'])->only('index', 'show', 'process');
    }

    public function index()
    {
        $affiliateWithdraws = AffiliateWithdraw::latest()
            ->when(request('name'), function ($query) {
                return $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . request('name') . '%'));
            })
            ->paginate();

        $title = $this->title;

        return view('affiliate-withdraw.index', compact('affiliateWithdraws', 'title'));
    }

    public function process(AffiliateWithdraw $affiliateWithdraw, Request $request)
    {
        if ($affiliateWithdraw->status === 'paid') {
            toast('This withdrawal has already been processed.', 'warning');
            return redirect()->back();
        }

        DB::transaction(function () use ($affiliateWithdraw) {
            // Lock the affiliate row for update
            $affiliate = Affiliate::where('id', $affiliateWithdraw->affiliate_id)
                ->lockForUpdate()
                ->firstOrFail();

            $amountBefore = $affiliate->balance;

            // Update withdraw status
            $affiliateWithdraw->status = StatusConst::PAID;
            $affiliateWithdraw->processed_at = now();
            $affiliateWithdraw->save();

            // Update balance
            $affiliate->balance -= $affiliateWithdraw->amount;
            $affiliate->save();

            // Save history
            $affiliate->affiliateHistories()->create([
                'affiliate_id'       => $affiliate->id,
                'affiliateable_type' => 'App\Models\AffiliateWithdraw',
                'affiliateable_id'   => $affiliateWithdraw->id,
                'amount'             => -$affiliateWithdraw->amount,
                'amount_before'      => $amountBefore,
            ]);
        });

        toast(alert_created_text($this->title), 'success');
        return redirect()->route('user.affiliate-withdraw');
    }
}
