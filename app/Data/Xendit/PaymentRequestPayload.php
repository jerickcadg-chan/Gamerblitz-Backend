<?php

namespace App\Data\Xendit;

use Spatie\LaravelData\Data;

class PaymentRequestPayload extends Data
{
    /**
     * @param array<string,mixed> $metadata
     */
    public function __construct(
        public string $reference_id,
        public string $type,
        public string $country,
        public string $currency,
        public float $request_amount,
        public string $capture_method,
        public string $channel_code,
        /** @var array<string, mixed> */
        public array $channel_properties,
        public string $description,
        public array $metadata,
    ) {
    }
}
