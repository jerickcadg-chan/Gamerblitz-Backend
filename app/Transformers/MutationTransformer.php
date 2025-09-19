<?php

namespace App\Transformers;

use App\Models\BalanceHistory;
use App\Models\Setting;
use League\Fractal\TransformerAbstract;

class MutationTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        //
    ];

    protected array $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     */
    public function transform(BalanceHistory $mutation): array
    {
        $convertedCurrencyCode = $mutation->balanceable->converted_currency_code;
        $exchangeRate = get_exchange_rate(Setting::getBaseCurrency(), $convertedCurrencyCode);
        $convertedAmount = $mutation->amount * $exchangeRate;
        $convertedLatestBalance = $mutation->latest_balance * $exchangeRate;
        return [
            'created_at' => $mutation->created_at,
            'description' => $mutation->description,
            'amount' => $mutation->amount,
            'latest_balance' => $mutation->latest_balance,
            'converted_amount' => $convertedAmount,
            'converted_latest_balance' => $convertedLatestBalance,
            'converted_currency_code' => $convertedCurrencyCode,
        ];
    }
}
