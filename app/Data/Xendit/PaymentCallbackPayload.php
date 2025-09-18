<?php

namespace App\Data\Xendit;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class PaymentCallbackPayload extends Data
{
    public function __construct(
        public string $event,
        public string $business_id,
        public string $created,
        public PaymentData $data,
    ) {
    }
}

class PaymentData extends Data
{
    public function __construct(
        public string $payment_id,
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
        /** @var \App\Data\CaptureData[] */
        public DataCollection $captures,
        public string $status,
        public PaymentDetailsData $payment_details,
        /** @var array<string, mixed> */
        public array $metadata,
        public string $created,
        public string $updated,
    ) {
    }
}

class CaptureData extends Data
{
    public function __construct(
        public string $capture_timestamp,
        public string $capture_id,
        public float $capture_amount,
    ) {
    }
}

class PaymentDetailsData extends Data
{
    public function __construct(
        public ?string $remark,
    ) {
    }
}
