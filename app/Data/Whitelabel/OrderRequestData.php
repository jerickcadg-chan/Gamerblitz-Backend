<?php

namespace App\Data\Whitelabel;

use Spatie\LaravelData\Data;

class OrderRequestData extends Data
{
    public function __construct(
        public string $item_code,
        public string $cust_account,
        public int $qty = 1,
        public ?string $partner_ref = null,
    ) {}
}
