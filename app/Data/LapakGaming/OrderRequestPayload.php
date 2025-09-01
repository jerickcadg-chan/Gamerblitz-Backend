<?php

namespace App\Data\LapakGaming;

use Spatie\LaravelData\Data;

class OrderRequestPayload extends Data
{
    public function __construct(
        // mandatory
        public int $count_order,
        public string $product_code, // mandatory if group_product empty
        public string $group_product = '', // mandatory if product_code empty

        // mandatory/optional
        public string $user_id = '', // optional for vouchers
        public string $additional_id = '',
        public string $additional_information = '',
        public string $orderdetail = '',

        // optional
        public string $country_code = '',
        public float $price = 0,
        public string $partner_reference_id = '',
        public string $override_callback_url = ''
    ) {
    }

    public function fill(mixed $array)
    {
        foreach ($array as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
