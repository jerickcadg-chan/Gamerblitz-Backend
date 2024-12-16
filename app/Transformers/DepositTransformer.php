<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class DepositTransformer extends TransformerAbstract
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
    public function transform($deposit): array
    {
        return [
            'code' => $deposit->code,
            'user' => $deposit->user->name,
            'payment_method' => $deposit->paymentMethod->name,
            'status' => $deposit->status_translated,
            'amount' => rp_format($deposit->amount),
            'unique_code' => $deposit->unique_code,
            'total_amount' => rp_format($deposit->total_amount),
            'paid_at' => parse_date_time($deposit->paid_at),
            'expired_at' => parse_date_time($deposit->expired_at),
            'updated_at' => parse_date_time($deposit->updated_at),
            'created_at' => parse_date_time($deposit->created_at),
        ];
    }
}
