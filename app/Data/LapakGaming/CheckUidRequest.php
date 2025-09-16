<?php

namespace App\Data\LapakGaming;

use Spatie\LaravelData\Data;

class CheckUidRequest extends Data
{
    public function __construct(
        public string $category_code,
        public string $user_id,
        public ?string $additional_id,
        public ?string $additional_information,
    ) {
    }
}
