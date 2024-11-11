<?php

namespace App\Services;

use App\Models\Balance;
use App\Models\BalanceHistory;

class BalanceService
{
    public static function update(Balance $balance, $request): Balance
    {
        $request['latest_balance'] = $balance->amount + $request['amount'];

        $balance->update([
            'amount' => $request['latest_balance']
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
            'description' => $request['description'] ?? null
        ]);
    }
}
