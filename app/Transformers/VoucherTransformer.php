<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Voucher;

class VoucherTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];

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
