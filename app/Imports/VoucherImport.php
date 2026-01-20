<?php

namespace App\Imports;

use App\Models\Voucher;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Concerns\ToModel;

class VoucherImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return Voucher|null
     */
    public function model(array $row)
    {
        if ($row[0] != null) {
            return new Voucher([
                'product_item_id'   => $row[0],
                'serial_number'     => $row[1],
                'password'          => $row[2],
                'capital'           => $row[3],
                'vendor'            => $row[4],
                'status'            => 'ready'
            ]);
        }
    }
}
