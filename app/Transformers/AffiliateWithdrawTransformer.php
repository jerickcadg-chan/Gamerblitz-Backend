<?php

namespace App\Transformers;

use App\Models\Affiliate;
use League\Fractal\TransformerAbstract;

class AffiliateWithdrawTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($affiliate)
    {
        return [
            'affiliate_id' => $affiliate->affiliate_id,
            'user_id' => $affiliate->user_id,
            'amount' => $affiliate->amount,
            'status' => $affiliate->status,
            'method' => $affiliate->method,
            'destination' => $affiliate->destination,
            'notes' => $affiliate->notes,
            'requested_at' => $affiliate->requested_at,
            'processed_at' => $affiliate->processed_at,
        ];
    }
}
