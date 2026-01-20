<?php

namespace App\Services;

use App\Constants\DefaultRole;
use App\Models\Deposit;
use App\Models\Balance;
use App\Constants\StatusConst;
use App\Models\BalanceHistory;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepositService
{
    /**
     * @param Deposit $deposit
     * @param string $status
     * @param string $amount
     * @return array
     */
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

            self::updateUserLevel($balance, $deposit->user);
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

            $deposit->status = StatusConst::PAID;
            $deposit->save();

            BalanceService::update($balance, [
                'balanceable_type' => Deposit::class,
                'balanceable_id' => $deposit->id,
                'amount' => $deposit->amount,
                'description' => "Topup Balance $deposit->code"
            ]);

            self::updateUserLevel($balance, $deposit->user);

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

    /**
     * @param string $userCurrency
     * @return float
     */
    public static function getDepositMinAmount(string $userCurrency)
    {
        $baseCurrency = Setting::getBaseCurrency();
        $exchangeRate = get_exchange_rate($baseCurrency, $userCurrency);
        $depositMinAmount = Setting::getByKey('deposit_min_amount') ?: 0;
        return $depositMinAmount * $exchangeRate;
    }

    /**
     * @param string $userCurrency
     * @return float|null
     */
    public static function getDepositMaxAmount(string $userCurrency)
    {
        $baseCurrency = Setting::getBaseCurrency();
        $exchangeRate = get_exchange_rate($baseCurrency, $userCurrency);
        $depositMaxAmount = Setting::getByKey('deposit_max_amount');
        return $depositMaxAmount ? $depositMaxAmount * $exchangeRate : null;
    }

    /**
     * @param Balance $balance
     * @param User $user
     */
    public static function updateUserLevel($balance, $user)
    {
        $totalDeposit = BalanceHistory::where('balance_id', $balance->id)
            ->where('balanceable_type', Deposit::class)->sum('amount');
        $level = self::getTierByBalance($totalDeposit);

        $user->assignRole($level);
    }

    /**
     * @param int $balance
     * @return string
     */
    public static function getTierByBalance($balance)
    {
        $balance = (int) $balance;

        $thresholds = [
            DefaultRole::RESELLER_VIP  => (int) Setting::getByKey('balance_vip'),
            DefaultRole::RESELLER_GOLD => (int) Setting::getByKey('balance_gold'),
            DefaultRole::RESELLER_SILVER => (int) Setting::getByKey('balance_silver'),
            DefaultRole::CUSTOMER => (int) Setting::getByKey('balance_public'),
        ];

        // sort descending by threshold value (just in case settings tidak terurut)
        uasort($thresholds, fn($a, $b) => $b <=> $a);

        foreach ($thresholds as $role => $min) {
            if ($balance >= $min) {
                return $role;
            }
        }

        return DefaultRole::CUSTOMER;
    }
}
