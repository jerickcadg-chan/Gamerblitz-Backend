<?php

namespace App\Transformers;

use App\Models\AffiliateHistory;
use App\Models\Setting;
use League\Fractal\TransformerAbstract;

class AffiliateHistoryTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(AffiliateHistory $affiliateHistory)
    {
        return [
            'id' => $affiliateHistory->id,
            'affiliate_id' => $affiliateHistory->affiliate_id,
            'amount' => $affiliateHistory->amount,
            'amount_before' => $affiliateHistory->amount_before,
            'latest_balance' => $affiliateHistory->latest_balance,
            'created_at' => $affiliateHistory->created_at,
            'description' => $affiliateHistory->description,
            'currency_code' => Setting::getBaseCurrency(),
        ];
    }
}
