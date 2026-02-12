<?php

namespace App\Data\LapakGaming;

use Spatie\LaravelData\Data;

class ProductItem extends Data
{
    public function __construct(
        public string $code,
        public string $name,
        public string $provider_code,
        public float $price,
        public float $process_time,
        public string $country_code,
        public string $status,
    ) {
    }
}

// {
//   "code": "ML-100-AUTO-3",
//   "name": "100 Diamonds Automation",
//   "provider_code": "AUTO-3",
//   "price": 1,
//   "process_time": 1,
//   "country_code": "id",
//   "status": "empty"
// }
