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
use App\Services\BillplzService;
use App\Services\DepositService;
use App\Services\HitpayService;
use App\Services\MpayService;
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

    public function store(DepositRequest $request)
    {
        $baseCurrency = Setting::getBaseCurrency();
        $userCurrency = $request->currency_code;
        $exchangeRateToBase = get_exchange_rate($userCurrency, $baseCurrency);

        $minAmount = DepositService::getDepositMinAmount($userCurrency);

        $paymentMethod = PaymentMethod::find($request->payment_method_id);

        if ($request->currency_code !== $paymentMethod->currency_code) {
            return api_status_warning('Invalid currency for payment method ' . $paymentMethod->name);
        }

        $amount = $request->amount;
        if ($amount < $minAmount) {
            return api_status_warning('Min deposit amount is ' . currency_format($minAmount, $userCurrency));
        }

        $adminFee = match ($paymentMethod->admin_type) {
            'percentage' => ceil($amount / ((100 - $paymentMethod->admin_fee) / 100)) - $amount,
            'nominal' => $paymentMethod->admin_fee,
            default => 0,
        };

        if ($paymentMethod->vendor === PaymentMethod::MANUAL) {
            $uniqueCode = $this->generateUniqueAmount($amount, $userCurrency);
            $adminFee = $uniqueCode;
        }

        $totalAmount = $amount + $adminFee;

        // Prevent negative deposit amounts
        if ($amount <= 0) {
            return api_status_warning('Invalid deposit amount');
        }

        $baseAmount = $amount * $exchangeRateToBase;
        $baseAdminFee = $adminFee * $exchangeRateToBase;
        $baseTotalAmount = $totalAmount * $exchangeRateToBase;

        $deposit = Deposit::create([
            'code' => "DP".date('ymd').rand(1000, 999999),
            'user_id' => $this->userId,
            'payment_method_id' => $paymentMethod->id,
            'currency_code' => $baseCurrency,
            'converted_currency_code' => $userCurrency,
            'exchange_rate' => $exchangeRateToBase,
            'amount' => $baseAmount,
            'admin_fee' => $baseAdminFee,
            'total_amount' => $baseTotalAmount,
            'converted_amount' => $amount,
            'converted_admin_fee' => $adminFee,
            'converted_total_amount' => $totalAmount,
            'expired_at' => now()->addHours(3),
            'status' => StatusConst::PENDING,
        ]);

        if ($paymentMethod->vendor === PaymentMethod::XENDIT) {
            app(XenditService::class)->createDepositXenditInvoice($deposit);
        }

        if ($paymentMethod->vendor === PaymentMethod::HITPAY) {
            app(HitpayService::class)->createDepositHitpayInvoice($deposit);
        }

        if ($paymentMethod->vendor === PaymentMethod::BILLPLZ) {
            app(BillplzService::class)->createDepositBillplzInvoice($deposit);
        }

        if ($paymentMethod->vendor === PaymentMethod::MPAY) {
            app(MpayService::class)->createDepositMpayInvoice($deposit);
        }

        return api_status_ok(transformer($deposit, new DepositTransformer()));
    }

    public function mutation(Request $request)
    {
        $userCurrency = $request->currency_code ?? Setting::getBaseCurrency();
        $exchangeRate = get_exchange_rate(Setting::getBaseCurrency(), $userCurrency);

        $mutations = BalanceHistory::with(['balance'])->latest()->whereHas('balance', function (Builder $query) {
            return $query->where('user_id', $this->userId);
        });

        $balance = Balance::where('user_id', $this->userId)->value('amount') ?? 0;

        return api_status_ok([
            'balance' => currency_format($balance),
            'pagination' => paginateTransformer($mutations, new MutationTransformer($userCurrency, $exchangeRate))
        ]);
    }

    public function metadata(Request $request)
    {
        $request->validate([
            'currency_code' => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
        ]);

        $userCurrency = $request->currency_code;

        return api_status_ok([
            'min_amount' => (string) DepositService::getDepositMinAmount($userCurrency),
            'currency_code' => $userCurrency,
        ]);
    }

    private function generateUniqueAmount(float $baseAmount, string $currency): float
    {
        switch (strtoupper($currency)) {
            case 'IDR':
                return rand(1, 999);

            default:
                return 0;
        }
    }
}
