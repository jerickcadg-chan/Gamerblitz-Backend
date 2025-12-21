<?php

namespace App\Services;

use App\Models\Balance;
use App\Models\BalanceHistory;

class BalanceService
{
    public static function update(Balance $balance, $request): Balance
    {
        // balance log check
        $latestBalance = $balance->amount + $request['amount'];

        $balanceCheck = $balance->histories()->latest()->first();

        if ($balanceCheck->amount == $request['amount']) {
            return $balance;
        }

        $balance->update([
            'amount' => $latestBalance
        ]);

        self::createBalanceLog($balance, $request);

        return $balance;
    }

    public static function createBalanceLog(Balance $balance, $request)
    {
        return BalanceHistory::create([
            'balance_id' => $balance->id,
            'balanceable_type' => $request['balanceable_type'],
            'balanceable_id' => $request['balanceable_id'],
            'amount' => $request['amount'],
            'latest_balance' => $request['latest_balance'],
            'description' => $request['description'] ?? null,
            'updated_by' => $request['updated_by'] ?? null
        ]);
    }
}
