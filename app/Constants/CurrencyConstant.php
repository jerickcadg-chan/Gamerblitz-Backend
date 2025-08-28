<?php

namespace App\Constants;

use Illuminate\Support\Facades\Cache;

class CurrencyConstant
{
    public const DEFAULT_BASE_CURRENCY = 'USD';

    protected static array $priority = ['USD', 'PHP', 'IDR'];

    public static function all(): array
    {
        $data = Cache::rememberForever('currencies', function () {
            return json_decode(
                file_get_contents(resource_path('data/currencies.json')),
                true
            );
        });

        // Prioritize fixed currencies
        uksort($data, function ($a, $b) {
            $posA = array_search($a, self::$priority, true);
            $posB = array_search($b, self::$priority, true);

            $posA = $posA === false ? PHP_INT_MAX : $posA;
            $posB = $posB === false ? PHP_INT_MAX : $posB;

            return $posA <=> $posB;
        });

        return $data;
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
