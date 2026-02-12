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

    public function __construct(public string $convertedCurrency, public float $exchangeRate = 1)
    {}

    /**
     * A Fractal transformer.
     */
    public function transform(BalanceHistory $mutation): array
    {
        $balanceable = $mutation->balanceable;
        if (empty($balanceable)) {
            return [
                'created_at' => $mutation->created_at,
                'description' => $mutation->description,
                'amount' => $mutation->amount,
                'latest_balance' => $mutation->latest_balance,
                'converted_amount' => 0,
                'converted_latest_balance' => 0,
                'converted_currency_code' => Setting::getBaseCurrency(),
            ];
        }
        $convertedAmount = $mutation->amount * $this->exchangeRate;
        $convertedLatestBalance = $mutation->latest_balance * $this->exchangeRate;
        return [
            'created_at' => $mutation->created_at,
            'description' => $mutation->description,
            'amount' => $mutation->amount,
            'latest_balance' => $mutation->latest_balance,
            'converted_amount' => $convertedAmount,
            'converted_latest_balance' => $convertedLatestBalance,
            'converted_currency_code' => $this->convertedCurrency,
        ];
    }
}
