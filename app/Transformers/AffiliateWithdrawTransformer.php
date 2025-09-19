<?php

namespace App\Transformers;

use App\Models\Setting;
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
            'currency_code' => Setting::getBaseCurrency(),
            'requested_at' => $affiliate->requested_at,
            'processed_at' => $affiliate->processed_at,
        ];
    }
}
