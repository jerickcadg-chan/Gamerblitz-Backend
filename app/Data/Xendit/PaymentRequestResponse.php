<?php

namespace App\Data\Xendit;

use Spatie\LaravelData\Data;

class PaymentRequestResponse extends Data
{
    public function __construct(
        public string $business_id,
        public string $reference_id,
        public string $payment_request_id,
        public string $type,
        public string $country,
        public string $currency,
        public float $request_amount,
        public string $capture_method,
        public string $channel_code,
        /** @var array<string, mixed> */
        public array $channel_properties,
        /** @var ActionData[] */
        public array $actions,
        public string $status,
        public string $description,
        /** @var array<string,mixed> $metadata */
        public array $metadata,
        public string $created,
        public string $updated,
    ) {
    }
}


class ActionData extends Data
{
    public function __construct(
        public string $type,
        public string $value,
        public string $descriptor,
    ) {
    }
}
