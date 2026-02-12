<?php

namespace App\Data\LapakGaming;

use Spatie\LaravelData\Data;

class Category extends Data
{
    public function __construct(
        public string $code,
        public string $name,
        public string $variant,
        public string $check_id,
        public string $country_code,
        /** @var FormData[] */
        public array $forms,
        /** @var OptionData[] */
        public ?array $servers = null,
    ) {}
}

class FormData extends Data
{
    public function __construct(
        public string $name,
        public string $type,
        /** @var OptionData[]|null */
        public ?array $options = null,
    ) {}
}

class OptionData extends Data
{
    public function __construct(
        public string $value,
        public string $name,
    ) {}
}

// {
//     "code": "AR",
//     "name": "Atlantica Rebirth",
//     "variant": "DIGITAL",
//     "check_id": "active",
//     "country_code": "id",
//     "forms": [
//         {
//             "name": "user_id",
//             "type": "tel"
//         },
//         {
//             "name": "additional_id",
//             "type": "option",
//             "options": [
//                 {
//                     "value": "",
//                     "name": "Pilih Server"
//                 },
//                 {
//                     "value": "All Server",
//                     "name": "All Server"
//                 }
//             ]
//         }
//     ],
//     "servers": [
//         {
//             "value": "",
//             "name": "Pilih Server"
//         },
//         {
//             "value": "All Server",
//             "name": "All Server"
//         }
//     ]
// }
