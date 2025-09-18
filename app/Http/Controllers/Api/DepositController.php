<?php

namespace App\Http\Controllers\Api;

use App\Constants\StatusConst;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Models\Balance;
use App\Models\BalanceHistory;
use App\Models\Deposit;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Transformers\DepositTransformer;
use App\Transformers\MutationTransformer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DepositController extends Controller
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
        $baseCurrency = Setting::getBaseCurrency();

        $uniqueCode = $this->generateUniqueAmount($request->amount, $baseCurrency);

        $paymentMethod = PaymentMethod::where('name', $request->payment_method)->first();

        $amount = $paymentMethod->admin_type === 'percentage'
            ? $request->amount + ($request->amount * ($paymentMethod->admin_fee / 100))
            : $request->amount + $paymentMethod->admin_fee;

        $deposit = Deposit::create([
            'code' => "DP".date('ymd').rand(1000, 999999),
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
            'balance' => currency_format($balance),
            'pagination' => paginateTransformer($mutations, new MutationTransformer())
        ]);
    }

    public function generateUniqueAmount(float $baseAmount, string $currency): float {
        switch (strtoupper($currency)) {
            case 'IDR':
                $uniqueCode = rand(1, 999);
                return $baseAmount + $uniqueCode;

            default:
                return 0;
        }
    }
}
