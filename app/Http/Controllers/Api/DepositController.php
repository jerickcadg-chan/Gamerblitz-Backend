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
use App\Services\DepositService;
use App\Services\XenditService;
use App\Transformers\DepositTransformer;
use App\Transformers\MutationTransformer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

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

    public function show(string $code)
    {
        try {
            $deposit = Deposit::with(['user', 'paymentMethod'])->where('code', $code)->firstOrFail();

            return api_status_ok(transformer($deposit, new DepositTransformer()));
        } catch (ModelNotFoundException $e) {
            return api_status_warning('Data not found', 404);
        }
    }

    public function store(DepositRequest $request, XenditService $xendit)
    {
        $baseCurrency = Setting::getBaseCurrency();
        $userCurrency = $request->currency_code;
        $exchangeRate = get_exchange_rate($baseCurrency, $userCurrency);

        $minAmount = DepositService::getDepositMinAmount($userCurrency);

        $paymentMethod = PaymentMethod::where('name', $request->payment_method)->first();

        if ($request->currency_code !== $paymentMethod->currency_code) {
            return api_status_warning('Invalid currency for payment method ' . $paymentMethod->name);
        }

        if ($request->amount < $minAmount) {
            return api_status_warning('Min deposit amount is ' . currency_format($minAmount, $userCurrency));
        }

        $uniqueCode = $this->generateUniqueAmount($request->amount, $baseCurrency);

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
            'status' => StatusConst::PENDING,
            'currency_code' => $baseCurrency,
            'converted_currency_code' => $userCurrency,
            'exchange_rate' => $exchangeRate,
        ]);

        if ($paymentMethod->vendor === PaymentMethod::XENDIT) {
            $xendit->createDepositXenditInvoice($deposit);
        }

        return api_status_ok(transformer($deposit, new DepositTransformer()));
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

    public function metadata(Request $request)
    {
        $request->validate([
            'currency_code' => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
        ]);

        $userCurrency = $request->currency_code;

        return api_status_ok([
            'min_amount' => DepositService::getDepositMinAmount($userCurrency),
            'currency_code' => $userCurrency,
        ]);
    }

    private function generateUniqueAmount(float $baseAmount, string $currency): float
    {
        switch (strtoupper($currency)) {
            case 'IDR':
                $uniqueCode = rand(1, 999);
                return $baseAmount + $uniqueCode;

            default:
                return 0;
        }
    }
}
