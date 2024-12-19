<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class VoucherTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($voucher)
    {
        return [
            'id' => $voucher->id,
            'product_item' => $voucher->productItem->name,
            'serial_number' => $voucher->serial_number,
            'password' => $voucher->PasswordDecrypted,
            'capital' => $voucher->capital,
            'vendor' => $voucher->vendor,
            'status' => $voucher->status,
        ];
    }
}
