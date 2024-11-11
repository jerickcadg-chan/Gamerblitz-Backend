<?php

namespace App\Http\Controllers\Api;

use App\Constants\StatusConst;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Models\Balance;
use App\Models\BalanceHistory;
use App\Models\Deposit;
use App\Models\PaymentMethod;
use App\Transformers\DepositTransformer;
use App\Transformers\MutationTransformer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DepositController extends Controller
{
    public $userId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->userId = auth()->user()->id ?? null;

            return $next($request);
        });
    }

    public function index()
    {
        $deposits = Deposit::with(['user', 'paymentMethod'])->latest()->where('user_id', $this->userId);

        return api_status_ok(paginateTransformer($deposits, new DepositTransformer()));
    }

    public function show($code)
    {
        try {
            $deposit = Deposit::with(['user', 'paymentMethod'])->where('code', $code)->firstOrFail();

            return api_status_ok(transformer($deposit, new DepositTransformer()));
        } catch (ModelNotFoundException $e) {
            return api_status_warning('Data not found', 404);
        }
    }

    public function store(DepositRequest $request)
    {
        $uniqueCode = rand(10, 100);

        $paymentMethod = PaymentMethod::find($request->payment_method_id);

        $amount = $paymentMethod->admin_type === 'percentage'
            ? $request->amount + ($request->amount * ($paymentMethod->admin_fee / 100))
            : $request->amount + $paymentMethod->admin_fee;

        $deposit = Deposit::create([
            'code' => "DP".date('ymd').rand(1000, 9999),
            'user_id' => $this->userId,
            'payment_method_id' => $paymentMethod->id,
            'amount' => $amount,
            'unique_code' => $uniqueCode,
            'total_amount' => $amount + $uniqueCode,
            'expired_at' => now()->addHours(3),
            'status' => StatusConst::PENDING
        ]);

        return api_status_ok($deposit);
    }

    public function mutation()
    {
        $mutations = BalanceHistory::with(['balance'])->latest()->whereHas('balance', function (Builder $query) {
            return $query->where('user_id', $this->userId);
        });

        $balance = Balance::where('user_id', $this->userId)->value('amount') ?? 0;

        return api_status_ok([
            'balance' => rp_format($balance),
            'pagination' => paginateTransformer($mutations, new MutationTransformer())
        ]);
    }
}
