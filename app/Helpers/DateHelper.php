<?php

use App\Models\Closing;
use Carbon\Carbon;

if (!function_exists('get_months')) {
    function get_months()
    {
        return array(
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        );
    }
}

if (!function_exists('get_month_simple')) {
    function get_month_simple($month)
    {
        $month = ((int) $month) - 1;
        $array =  array(
            'JAN',
            'FEB',
            'MAR',
            'APR',
            'MEI',
            'JUN',
            'JUL',
            'AGU',
            'SEP',
            'OKT',
            'NOV',
            'DES',
        );

        return $array[$month];
    }
}

if (!function_exists('get_month_name')) {
    function get_month_name($month)
    {
        $m = $month - 1;
        return get_months()[$m] ?? 'Undefined';
    }
}

if (!function_exists('get_month_romawi')) {
    function get_month_roman()
    {
        switch (date('m')) {
            case 1;
                return 'I';
            case 2;
                return 'II';
            case 3;
                return 'III';
            case 4;
                return 'IV';
            case 5;
                return 'V';
            case 6;
                return 'VI';
            case 7;
                return 'VII';
            case 8;
                return 'VIII';
            case 9;
                return 'IX';
            case 10;
                return 'X';
            case 11;
                return 'XI';
            case 12;
                return 'XII';
        }
    }
}

if (!function_exists('parse_date_format')) {
    function parse_date_format($date)
    {
        return Carbon::parse($date)->format('Y-m-d');
    }
}

if (!function_exists('parse_date')) {
    function parse_date($date = null)
    {
        if ($date == null) {
            return '-';
        }
        Carbon::setLocale('id');
        //        $date = \Carbon\Carbon::parse($date)->translatedFormat("%d %B %Y");
        $date = Carbon::parse($date);
        return $date->format('d') . '-' . get_month_simple($date->format('m')) . '-' . $date->format('Y');
    }
}

if (!function_exists('parse_date_full')) {
    function parse_date_full($date = null)
    {
        if ($date == null) {
            return '-';
        }
        Carbon::setLocale('id');
        //        $date = \Carbon\Carbon::parse($date)->translatedFormat("%d %B %Y");
        $date = Carbon::parse($date);
        return $date->format('d') . ' ' . get_month_name($date->format('m')) . ' ' . $date->format('Y');
    }
}

if (!function_exists('parse_date_time')) {
    function parse_date_time($date = null)
    {
        if ($date == null) {
            return '-';
        }
        Carbon::setLocale('id');
        //        $date = \Carbon\Carbon::parse($date)->translatedFormat("%d %B %Y, %I:%M:%S %p");
        $date = Carbon::parse($date);
        return $date->format('d') . '-' . get_month_simple($date->format('m')) . '-' . $date->format('Y') . ', ' . \Carbon\Carbon::parse($date)->translatedFormat("%H:%M:%S");
        //        return $date;
    }
}

if (!function_exists('parse_date_time_full')) {
    function parse_date_time_full($date = null)
    {
        if ($date == null) {
            return '-';
        }
        Carbon::setLocale('id');

        $date = Carbon::parse($date);
        return $date->format('d') . ' ' . get_month_name($date->format('m')) . ' ' . $date->format('Y') . ', ' . \Carbon\Carbon::parse($date)->translatedFormat("%H:%M:%S");
    }
}

if (!function_exists('parse_month_year')) {
    function parse_month_year($date = null)
    {
        if ($date == null) {
            return '-';
        }

        Carbon::setLocale('id');

        $date = Carbon::parse($date);
        return get_month_name($date->format('m')) . ' ' . $date->format('Y');
    }
}

if (!function_exists('parse_year')) {
    function parse_year($date = null)
    {
        if ($date == null) {
            return '-';
        }

        Carbon::setLocale('id');

        $date = Carbon::parse($date);
        return $date->format('Y');
    }
}

if (!function_exists('parse_start_and_end_date')) {
    function parse_start_and_end_date($start, $end)
    {
        $start = Carbon::parse($start)->format('m/d/Y');
        $end = Carbon::parse($end)->format('m/d/Y');

        return $start . " - " . $end;
    }
}

if (!function_exists('get_years')) {
    function get_years()
    {
        $year = collect();

        for ($i = 2010; $i < 2050; $i++) {
            $year->push($i);
        }

        return $year->toArray();
    }
}

if (!function_exists('get_start_and_end_date')) {
    function get_start_and_end_date($dates)
    {
        $range = explode(' - ', $dates);

        $start = Carbon::createFromFormat('m/d/Y', $range[0])->toDateString();
        $end = Carbon::createFromFormat('m/d/Y', $range[1])->toDateString();
        return [
            'start_date' => $start,
            'end_date' => $end
        ];
    }
}

if (!function_exists('get_date_ranges')) {
    function get_date_ranges($dates)
    {
        $range = explode(' - ', $dates);
        $start = Carbon::createFromFormat('m/d/Y', $range[0]);
        $end = Carbon::createFromFormat('m/d/Y', $range[1]);

        $dates = [];

        for ($date = $start; $date->lte($end); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }
}

if (!function_exists('get_date_array')) {
    function get_date_array($start, $end)
    {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);

        $dates = [];

        for ($date = $start; $date->lte($end); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }
}
