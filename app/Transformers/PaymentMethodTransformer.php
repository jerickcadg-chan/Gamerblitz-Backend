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
            'category' => $paymentMethod->category,
            'name' => $paymentMethod->name,
            'account_number' => $paymentMethod->account_number,
            'display_name' => $paymentMethod->display_name,
            'admin_fee' => $paymentMethod->admin_fee_translated,
            'admin_fee_raw' => $paymentMethod->admin_fee,
            'admin_type' => $paymentMethod->admin_type,
            'vendor' => $paymentMethod->vendor,
            'type' => $paymentMethod->type,
            'slug' => $paymentMethod->slug,
            'picture_url' => $paymentMethod->picture_url,
            'currency_code' => $paymentMethod->currency_code,
            'description' => $paymentMethod->description,
            'additional_input' => $paymentMethod->additional_input,
        ];
    }
}
