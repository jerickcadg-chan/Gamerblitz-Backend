<?php

namespace App\Data\LapakGaming;

use Spatie\LaravelData\Data;

class OrderResponse extends Data
{
    public function __construct(
        public string $code,
        public ?ResponseData $data = null,
    ) {
    }
}

class ResponseData extends Data
{
    public function __construct(
        public string $tid,
        public float $total_price,
        public string $display_name = '',
    ) {
    }
}

// Success response
// -----------------
// {
//   "code": "SUCCESS",
//   "data": {
//     "tid": "R161582713591477186",
//     "total_price": 33120
//   }
// }
//
//
// Test data to simulate each status
// -------------------------
// SUCCESS
// product_code : ML78_8-S2

// SUCCESS (for voucher)
// product_code : VCGS330-S22

// PRICE_NOT_MATCH
// product_code : ML78_8-S2
// price : 999999

// PRODUCT_NOT_FOUND
// product_code : ASD

// PRODUCT_EMPTY
// product_code : ML156_16-S42

// PROVIDER_NOT_FOUND
// product_code : ML234_23-S2

// PROVIDER_INACTIVE
// product_code : ML625_81-S2

// INSUFFICIENT_BALANCE
// product_code : ML7740_1548-S42

// SUCCESS with pending order
// product_code : ML4649_883-S42
