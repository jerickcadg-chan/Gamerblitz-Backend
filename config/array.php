<?php

use App\Constants\StatusConst;

return [
    'product' => [
        'category' => ['game', 'voucher', 'operator', 'other'],
        'status' => [\App\Models\Product::ACTIVE, \App\Models\Product::INACTIVE]
    ],
    'default_role' => [
        \App\Constants\DefaultRole::SUPER_ADMIN, \App\Constants\DefaultRole::CUSTOMER, \App\Constants\DefaultRole::RESELLER
    ],
    'menu' => [
        'dashboard',
        'product' => ['product', 'product_item', 'voucher'],
        'order',
        'report',
        'promo' => ['discount', 'slider'],
        'user' => ['user', 'guest', 'customer', 'role'],
        'digi' => ['digi_product_create', 'digi_transaction', 'digi_transaction_create']
    ],
    'order' => [
        'payment_status' => [
            'pending' => \App\Models\Order::PENDING,
            'settlement' => \App\Models\Order::SETTLEMENT,
            'refunded' => \App\Models\Order::REFUNDED,
        ],
        'order_status' => [
            'waiting-payment' => \App\Models\Order::WAITING_PAYMENT,
            'in-process' => \App\Models\Order::INPROCESS,
            'done' => \App\Models\Order::DONE,
            'expired' => \App\Models\Order::EXPIRED,
            'canceled' => \App\Models\Order::CANCELED,
        ],
        'expired_hours' => env('EXPIRED_HOURS')
    ],
    'deposit' => [
        'status' => [StatusConst::PENDING, StatusConst::PAID, StatusConst::EXPIRED]
    ],
    'bangjeff' => [
        'url' => env('BANGJEFF_URL'),
        'api_key' => env('BANGJEFF_APIKEY'),
        'ip' => env('BANGJEFF_IP'),
    ],
    'xendit' => [
        'url' => env('XENDIT_URL'),
        'token' => env('XENDIT_TOKEN'),
        'callback_token' => env('XENDIT_CALLBACK_TOKEN')
    ],
    'mail' => [
        'notification' => env('MAIL_NOTIFICATION'),
        'no_reply' => config('MAIL_NO_REPLY')
    ],
    'store' => [
        'url' => env('STORE_URL')
    ],
    'enable_log' => env('ENABLE_LOG'),
    'discount' => [
        'disc_type' => [
            [
                'value' => 'percentage',
                'desc' => 'Persentase (%)'
            ],
            [
                'value' => 'nominal',
                'desc' => 'Nominal (Rp)'
            ]
        ],
        'disc_type_validation' => ['percentage', 'nominal'],
        'product_type' => [
            [
                'value' => \App\Models\Discount::ALL,
                'desc' => 'Berlaku untuk semua produk'
            ],
            [
                'value' => \App\Models\Discount::PRODUCT_TYPE,
                'desc' => 'Berlaku untuk produk tertentu'
            ],
            [
                'value' => \App\Models\Discount::PRODUCT_ITEM,
                'desc' => 'Berlaku untuk produk item tertentu'
            ]
        ],
        'product_type_validation' => [
            \App\Models\Discount::ALL,
            \App\Models\Discount::PRODUCT_TYPE,
            \App\Models\Discount::PRODUCT_ITEM
        ],
    ],
    'setting' => [
        'pin' => '$2a$12$ZfmtR2QbolbXYIcW556KF.fQOcM/4xJX4X7hI1.jAO4lqhoMvRAbS'
    ],
    'payment_method' => [
        'admin_fee' => [
            'bca' => 0,
            'va_mandiri' => 4500,
            'va_bri' => 4500,
            'va_bsi' => 0,
            'gopay' => '1.4%',
            'dana' => '2%',
        ]
    ]
];
