<?php

namespace App\Transformers;

use App\Models\BalanceHistory;
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
        return [
            'created_at' => parse_date_time($mutation->created_at),
            'description' => $mutation->description,
            'amount' => rp_format($mutation->amount),
            'latest_balance' => rp_format($mutation->latest_balance),
        ];
    }
}
