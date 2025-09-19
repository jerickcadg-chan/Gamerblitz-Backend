<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Balance;
use App\Constants\StatusConst;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class DepositService
{
    public static function updateStatus(Deposit $deposit, $status, $amount = ''): array
    {
        if ($deposit->status == $status) {
            return [
                'status' => false,
                'message' => 'Wrong status'
            ];
        }

        if ($amount) {
            $deposit->amount = $amount;
            $deposit->unique_code = 0;
            $deposit->total_amount = $amount;
        }

        $deposit->status = $status;
        $deposit->save();

        if ($status !== StatusConst::EXPIRED) {
            $balance = Balance::query()->lockForUpdate()->firstOrCreate(
                [
                    'user_id' => $deposit->user_id
                ],
                [
                    'amount' => 0
                ]
            );

            BalanceService::update($balance, [
                'balanceable_type' => Deposit::class,
                'balanceable_id' => $deposit->id,
                'amount' => $deposit->total_amount,
                'description' => "Topup Balance $deposit->code"
            ]);
        }

        return [
            'status' => true,
            'data' => $deposit
        ];
    }

    public function handlePaymentSettlement(Deposit $deposit)
    {
        if ($deposit->status === StatusConst::EXPIRED) {
            return 'Deposit expired';
        }

        try {
            DB::beginTransaction();

            $balance = Balance::query()
                ->lockForUpdate()
                ->firstOrCreate(
                    ['user_id' => $deposit->user_id],
                    ['amount' => 0]
                );

            $deposit->status === StatusConst::PAID;
            $deposit->save();

            BalanceService::update($balance, [
                'balanceable_type' => Deposit::class,
                'balanceable_id' => $deposit->id,
                'amount' => $deposit->amount,
                'description' => "Topup Balance $deposit->code"
            ]);

            DB::commit();

            return null;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function handlePaymentExpired(Deposit $deposit)
    {
        $deposit->status === StatusConst::EXPIRED;
        $deposit->save();

        return null;
    }

    public static function getDepositMinAmount(string $userCurrency)
    {
        $baseCurrency = Setting::getBaseCurrency();
        $exchangeRate = get_exchange_rate($baseCurrency, $userCurrency);
        $depositMinAmount = Setting::getByKey('deposit_min_amount');
        return $depositMinAmount * $exchangeRate;
    }
}
