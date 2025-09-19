<?php

namespace App\Http\Controllers;

use App\Models\AffiliateWithdraw;

class AffiliateWithdrawController extends Controller
{
    private string $title;

    public function __construct()
    {
        $this->title = 'Affiliate Withdraw';

        $this->middleware(['permission:View Withdraw'])->only('index', 'show');
        $this->middleware(['permission:Create Withdraw'])->only(['create', 'store']);
        $this->middleware(['permission:Edit Withdraw'])->only('edit', 'update');
        $this->middleware(['permission:Delete Withdraw'])->only('destroy');
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
}
