<?php

namespace App\Data\LapakGaming;

use Spatie\LaravelData\Data;

class BestProductItem extends Data
{
    public function __construct(
        public string $code,
        public string $country_code,
        public string $name,
        public float $price,
        public string $stock_variant,
        public float $process_time,
    ) {
    }
}

// {
//   "code": "ML40_4",
//   "country_code": "id",
//   "name": "44 Diamonds ( 40 + 4 Bonus )",
//   "price": 11415,
//   "stock_variant": "",
//   "process_time": 0
// }
