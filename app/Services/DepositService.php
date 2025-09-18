<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Balance;
use App\Models\Order;
use App\Models\Setting;

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

        if ($status !== Order::EXPIRED) {
            $balance = Balance::firstOrCreate(
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

    public static function getDepositMinAmount(string $userCurrency)
    {
        $baseCurrency = Setting::getBaseCurrency();
        $exchangeRate = get_exchange_rate($baseCurrency, $userCurrency);
        $depositMinAmount = Setting::getByKey('deposit_min_amount');
        return $depositMinAmount * $exchangeRate;
    }
}
