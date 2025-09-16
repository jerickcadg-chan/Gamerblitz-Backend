<?php

namespace App\Data\LapakGaming;

use Spatie\LaravelData\Data;

class CheckUidResponse extends Data
{
    public function __construct(
        public string $code,
        public ?CheckUidData $data,
    ) {}
}

class CheckUidData extends Data
{
    public function __construct(
        public string $name,
    ) {}
}
