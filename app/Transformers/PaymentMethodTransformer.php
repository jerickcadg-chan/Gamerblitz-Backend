<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\PaymentMethod;

class PaymentMethodTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(PaymentMethod $paymentMethod)
    {
        return [
            'id' => $paymentMethod->id,
            'name' => $paymentMethod->name,
            'display_name' => $paymentMethod->display_name,
            'admin_fee' => $paymentMethod->admin_fee_translated,
            'admin_fee_raw' => $paymentMethod->admin_fee,
            'admin_type' => $paymentMethod->admin_type,
            'vendor' => $paymentMethod->vendor,
            'slug' => $paymentMethod->slug,
            'picture_url' => $paymentMethod->picture->url
        ];
    }
}
