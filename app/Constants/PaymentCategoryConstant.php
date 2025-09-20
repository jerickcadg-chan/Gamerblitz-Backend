<?php

namespace App\Constants;

use ReflectionClass;

class PaymentCategoryConstant
{
    const QR            = 'QR';
    const EWALLET       = 'E-Wallet';
    const BANK_VA       = 'Bank Virtual Account';
    const RETAIL        = 'Retail Outlet';
    const CARDS         = 'Credit / Debit Card';
    const BANK_TRANSFER = 'Bank Transfer';
    const CASH_PICKUP   = 'Cash Pickup';
    const PAYLATER      = 'PayLater / Installment';
    const REMITTANCE    = 'Remittance';
    const CRYPTO        = 'Crypto';
    const BALANCE       = 'Account Balance';
    const OTHERS        = 'Others';

    public static function all(): array
    {
        return (new ReflectionClass(__CLASS__))->getConstants();
    }
}
