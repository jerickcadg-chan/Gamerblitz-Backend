<?php

namespace App\Constants;

use Illuminate\Support\Facades\Cache;

class CountryConstant
{
    protected static array $priority = [
        "PH",
        "ID",
        "MY",
        "TH",
        "US",
        "BR",
        "VN",
    ];

    public static function all(): array
    {
        $data = Cache::rememberForever('countries', function () {
            return json_decode(
                file_get_contents(resource_path('data/countries.json')),
                true
            );
        });

        // Prioritize fixed countries
        uksort($data, function ($a, $b) {
            $posA = array_search($a, self::$priority, true);
            $posB = array_search($b, self::$priority, true);

            $posA = $posA === false ? PHP_INT_MAX : $posA;
            $posB = $posB === false ? PHP_INT_MAX : $posB;

            return $posA <=> $posB;
        });

        return $data;
    }

    public static function name(string $code): ?string
    {
        $data = self::all();
        return $data[strtoupper($code)] ?? null;
    }
}
