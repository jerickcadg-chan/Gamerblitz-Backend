<?php

use App\Constants\StatusConst;

return [
    'product' => [
        'status' => [\App\Models\Product::ACTIVE, \App\Models\Product::INACTIVE]
    ],
    'default_role' => [
        \App\Constants\DefaultRole::SUPER_ADMIN,
        \App\Constants\DefaultRole::CUSTOMER,
        \App\Constants\DefaultRole::RESELLER_SILVER,
        \App\Constants\DefaultRole::RESELLER_GOLD,
        \App\Constants\DefaultRole::RESELLER_VIP,
    ],
    'menu' => [
        'dashboard',
        'product' => ['product', 'product_item', 'product_item_category', 'product_category'],
        'order',
        'report',
        'promo' => ['discount', 'slider', 'flash_sale'],
        'statistic' => ['statistic_order', 'statistic_user'],
        'user' => ['user', 'guest', 'customer', 'role', 'affiliate_withdraw'],
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
            'on-process' => \App\Models\Order::INPROCESS,
            'done' => \App\Models\Order::DONE,
            'expired' => \App\Models\Order::EXPIRED,
            'canceled' => \App\Models\Order::CANCELED,
        ],
        'status' => [
            StatusConst::PENDING,
            StatusConst::ON_PROCESS,
            StatusConst::SUCCESS,
            StatusConst::FAILED,
            StatusConst::DELAY,
            StatusConst::EXPIRED,
            StatusConst::REFUNDED
        ],
        'expired_hours' => env('EXPIRED_HOURS')
    ],
    'deposit' => [
        'min_amount' => 500,
        'status' => [StatusConst::PENDING, StatusConst::PAID, StatusConst::EXPIRED]
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
                'desc' => 'Applies to all products'
            ],
            [
                'value' => \App\Models\Discount::PRODUCT_TYPE,
                'desc' => 'Applies for specific products'
            ],
            [
                'value' => \App\Models\Discount::PRODUCT_ITEM,
                'desc' => 'Applies for specific product items'
            ]
        ],
        'product_type_validation' => [
            \App\Models\Discount::ALL,
            \App\Models\Discount::PRODUCT_TYPE,
            \App\Models\Discount::PRODUCT_ITEM
        ],
    ],
    'setting' => [
        'pin' => '$2a$12$ZfmtR2QbolbXYIcW556KF.fQOcM/4xJX4X7hI1.jAO4lqhoMvRAbS',
    ],
    'mitra-gamers' => [
        'token' => env('MITRA_GAMERS_TOKEN'),
        'url' => env('MITRA_GAMERS_URL')
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
    ],
    'mail_no_reply' => env('MAIL_NO_REPLY'),
    'mail_notification' => env('MAIL_NOTIFICATION'),
    'expired_hours' => env('EXPIRED_HOURS'),
    'store_url' => env('STORE_URL')
];
