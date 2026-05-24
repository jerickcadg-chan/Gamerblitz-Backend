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
    public function transform($affiliateWithdraw)
    {
        return [
            'affiliate_id' => $affiliateWithdraw->affiliate_id,
            'user_id' => $affiliateWithdraw->user_id,
            'amount' => $affiliateWithdraw->amount,
            'status' => $affiliateWithdraw->status,
            // amount will be converted to balance, which use system base currency
            'currency_code' => Setting::getBaseCurrency(),
            'requested_at' => $affiliateWithdraw->requested_at,
            'processed_at' => $affiliateWithdraw->processed_at,
        ];
    }
}
