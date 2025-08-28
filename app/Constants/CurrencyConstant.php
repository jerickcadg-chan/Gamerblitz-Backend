<?php

namespace App\Constants;

use Illuminate\Support\Facades\Cache;

class CurrencyConstant
{
    public const DEFAULT_BASE_CURRENCY = 'USD';

    public static function all(): array
    {
        return Cache::rememberForever('currencies', function () {
            return json_decode(
                file_get_contents(resource_path('data/currencies.json')),
                true
            );
        });
    }

    public static function metadata(string $code): ?array
    {
        $data = self::all();
        return $data[$code] ?? null;
    }

    public static function symbol(string $code): ?string
    {
        return self::metadata($code)['symbol'] ?? null;
    }

    public static function countryByCode(string $code): ?string
    {
        return self::metadata($code)['country'] ?? null;
    }

    public static function codeByCountry(string $country): ?string
    {
        foreach (self::all() as $code => $info) {
            if (strcasecmp($info['country'], $country) === 0) {
                return $code;
            }
        }
        return null;
    }

    public static function name(string $code): ?string
    {
        return self::metadata($code)['name'] ?? null;
    }

    public static function locale(string $code): ?string
    {
        return self::metadata($code)['locale'] ?? null;
    }
}
