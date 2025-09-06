<?php

use App\Constants\CurrencyConstant;
use App\Models\Setting;

if (!function_exists('idr_format')) {
    function idr_format($price, $zeroValue = 'IDR 0')
    {
        return (int) $price != 0 ? "IDR ". number_format($price,0,',','.') : $zeroValue;
    }
}

if (!function_exists('rp_format')) {
    function rp_format($price, $zeroValue = 'Rp. 0')
    {
        return (int) $price != 0 ? "Rp ". number_format($price,0,',','.') : $zeroValue;
    }
}

if (!function_exists('numbering')) {
    function numbering($value): int|string
    {
        return (int) $value != 0 ? number_format($value,0,',','.') : 0;
    }
}

if (!function_exists('currency_format')) {
    /**
     * Number formater based on locale
     *
     * @param float|int|null $amount
     * @param string|null $currencyCode default dari CurrencyConstant::DEFAULT_BASE_CURRENCY
     * @param string|null $zeroValue custom if amount is 0
     * @return string
     */
    function currency_format(float|int|null $amount, ?string $currencyCode = null, ?string $zeroValue = null): string
    {
        $currencyCode = $currencyCode ?: Setting::getByKey(Setting::KEY_BASE_CURRENCY);
        $meta = CurrencyConstant::metadata($currencyCode);

        if (!$meta) {
            return $currencyCode . ' ' . number_format($amount, 2);
        }

        $locale = $meta['locale'] ?? 'en-US';

        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        $formatted = $formatter->formatCurrency($amount, $currencyCode);

        $symbol = CurrencyConstant::symbol($currencyCode);

        if ($symbol && str_starts_with($formatted, $symbol) && !str_starts_with($formatted, $symbol.' ')) {
            $formatted = preg_replace('/^'.preg_quote($symbol, '/').'/', $symbol.' ', $formatted);
        }

        if ($formatted === false) {
            $symbol = $meta['symbol'] ?? $currencyCode;
            $formatted = $symbol . '' . number_format($amount, 2, $meta['decimal'] ?? '.', ',');
        }

        if ((int)$amount === 0 && $zeroValue) {
            return $zeroValue;
        }

        return $formatted;
    }
}

if (!function_exists('terbilang')) {
    function terbilang($nilai)
    {
        if($nilai<0) {
            $hasil = "minus ". trim(penyebut($nilai));
        } else {
            $hasil = trim(penyebut($nilai));
        }
        return $hasil .' rupiah';
    }

    function penyebut($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " ". $huruf[$nilai];
        } else if ($nilai <20) {
            $temp = penyebut($nilai - 10). " belas";
        } else if ($nilai < 100) {
            $temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = penyebut($nilai/1000000) . " juta" . penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai,1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai,1000000000000));
        }
        return $temp;
    }

    function get_alphabet($number)
    {
        $alphabet = array('a', 'b', 'c', 'd', 'e',
                       'f', 'g', 'h', 'i', 'j',
                       'k', 'l', 'm', 'n', 'o',
                       'p', 'q', 'r', 's', 't',
                       'u', 'v', 'w', 'x', 'y',
                       'z'
                   );

        return $alphabet[$number];
    }
}

if (!function_exists('calculate_exchange_rate')) {
    /**
     * Calculate exchange rate between two currency using USD as pivot currency
     */
    function pivot_exchange_rate($sourceToUSD, $targetToUSD) {
        return $sourceToUSD / $targetToUSD;
    }
}
