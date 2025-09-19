<?php

namespace App\Transformers;

use App\Models\Deposit;
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
    public function transform(Deposit $deposit): array
    {
        return [
            'code' => $deposit->code,
            'user' => $deposit->user->name,
            'payment_method' => $deposit->paymentMethod->name,
            'status' => $deposit->status,
            'amount' => $deposit->amount,
            'admin_fee' => $deposit->admin_fee,
            'unique_code' => $deposit->unique_code,
            'total_amount' => $deposit->total_amount,
            'currency_code' => $deposit->currency_code,
            'converted_currency_code' => $deposit->converted_currency_code,
            'converted_amount' => $deposit->converted_amount,
            'converted_admin_fee' => $deposit->converted_admin_fee,
            'converted_unique_code' => $deposit->converted_unique_code,
            'converted_total_amount' => $deposit->converted_total_amount,
            'payment_url' => $deposit->payment_url,
            'payment_code' => $deposit->payment_code,
            'payment_descriptor' => $deposit->payment_descriptor,
            'payment_id' => $deposit->payment_id,
            'paid_at' => $deposit->paid_at,
            'expired_at' => $deposit->expired_at,
            'updated_at' => $deposit->updated_at,
            'created_at' => $deposit->created_at,
        ];
    }
}
