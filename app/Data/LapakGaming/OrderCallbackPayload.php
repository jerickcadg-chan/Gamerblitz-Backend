<?php

namespace App\Data\LapakGaming;

use Spatie\LaravelData\Data;

class OrderCallbackPayload extends Data
{
    public function __construct(
        public string $code,
        public OrderCallbackData $data,
    ) {}
}

class OrderCallbackData extends Data
{
    public function __construct(
        public string $status,
        public string $tid,
        public string $reference_id,
        /** @var Transaction[] */
        public array $transactions,
    ) {}
}

class Transaction extends Data
{
    public function __construct(
        public string $id,
        public string $product_name,
        public string $note,
        public string $status,
        public ?string $voucher_code = "",
    ) {}
}

// {
//     "code": "SUCCESS",
//     "data": {
//         "status": "SUCCESS | PENDING | REFUNDED",
//         "tid": "RA123123123132",
//         "reference_id": "PARTNER_REFERENCE_ID_123",
//         "transactions": [
//             {
//                 "id": "38",
//                 "product_name": "172 Diamonds",
//                 "note": "Sukses Terkirim - 2021-03-16 00:28:20 WIB",
//                 "status": "success",
//                 "voucher_code": ""
//             },
//             {
//                 "id": "39",
//                 "product_name": "172 Diamonds",
//                 "note": "Sukses Terkirim - 2021-03-16 00:28:20 WIB",
//                 "status": "success",
//                 "voucher_code": ""
//             },
//             {
//                 "id": "40",
//                 "product_name": "172 Diamonds",
//                 "note": "Sukses Terkirim - 2021-03-16 00:28:20 WIB",
//                 "status": "success",
//                 "voucher_code": ""
//             },
//             {
//                 "id": "41",
//                 "product_name": "172 Diamonds",
//                 "note": "Sukses Terkirim - 2021-03-16 00:28:20 WIB",
//                 "status": "success",
//                 "voucher_code": ""
//             }
//         ]
//     }
// }
