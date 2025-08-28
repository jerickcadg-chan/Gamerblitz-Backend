<?php

namespace App\Constants;

use Sheenazien8\Konstantiq\ConstanstAbstraction;

class CurrencyConstant extends ConstanstAbstraction
{
    public const BASE_CURRENCY = 'PHP';

    public const IDR = 'IDR';
    public const PHP = 'PHP';
    public const USD = 'USD';

    // Metadata map
    private const DATA = [
        'IDR' => ['symbol' => 'Rp', 'country' => 'ID'],
        'PHP' => ['symbol' => '₱', 'country' => 'PH'],
        'USD' => ['symbol' => '$', 'country' => 'US'],
    ];

    public static function symbol(string $code): ?string
    {
        return self::DATA[$code]['symbol'] ?? null;
    }

    public static function codeByCountry(string $country): ?string
    {
        foreach (self::DATA as $code => $info) {
            if ($info['country'] === strtoupper($country)) {
                return $code;
            }
        }
        return null;
    }

    public static function countryByCode(string $code): ?string
    {
        return self::DATA[$code]['country'] ?? null;
    }
}

