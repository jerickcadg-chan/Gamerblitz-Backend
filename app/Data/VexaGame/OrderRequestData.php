<?php

namespace App\Data\VexaGame;

use Spatie\LaravelData\Data;

class OrderRequestData extends Data
{
    public function __construct(
        public string $code,
        public string $customer_no,
        public string $payment_method = 'balance',
        public int $qty = 1,
        public ?string $partner_ref_id = null,
    ) {}
}
