<?php

namespace App\Data\LapakGaming;

use Spatie\LaravelData\Data;

class ProductUpdateCallbackPayload extends Data
{
    public function __construct(
        public ProductData $data,
        public MetaData $meta,
    ) {}
}

class ProductData extends Data
{
    public function __construct(
        public string $code,
        public string $name,
        public string $provider_code,
        public int $price,
        public string $status,
    ) {}
}

class MetaData extends Data
{
    public function __construct(
        public string $level,
        public int $unix_timestamp,
    ) {}
}

// {
//     "data": {
//         "code": "ML-100-S102",
//         "name": "100 Diamonds Automation",
//         "provider_code": "S102",
//         "price": 9565,
//     "status": "available"
// },
//     "meta": {
//         "level": "master",
//         "unix_timestamp": 1707470882
//     }
// }

