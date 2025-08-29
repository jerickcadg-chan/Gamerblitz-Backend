<?php

namespace App\Dto\LapakGaming;

class OrderRequestPayload
{
    public function __construct(
        // mandatory
        public int $count_order,
        public string $product_code, // mandatory if group_product empty
        public string $group_product = '', // mandatory if product_code empty

        // optional
        public string $user_id = '',
        public string $additional_id = '',
        public string $additional_information = '',
        public string $orderdetail = '',
        public string $country_code = '',
        public float $price = 0,
        public string $partner_reference_id = '',
        public string $override_callback_url = ''
    ) {
    }

    public function toArray(): array
    {
        $vars = get_object_vars($this);

        return collect($vars)
            ->filter()
            ->mapWithKeys(fn ($value, $key) => [$key => $value])
            ->toArray();
    }
}
