<?php

namespace App\Constants;

use Sheenazien8\Konstantiq\ConstanstAbstraction;

class ProviderConstant extends ConstanstAbstraction
{
    public const LAPAKGAMING = 'lapakgaming';

    public const AVAILABLE_PROVIDER = [
        self::LAPAKGAMING => 'LapakGaming'
    ];

    public const AVAILABLE_COUNTRY = [
        "id" => "Indonesia",
        "my" => "Malaysia",
        "ph" => "Philippines",
        "th" => "Thailand",
        "us" => "United States",
        "br" => "Brazil",
        "vn" => "Vietnam",
    ];
}
