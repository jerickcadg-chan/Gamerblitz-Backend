<?php

namespace App\Constants;

use Sheenazien8\Konstantiq\ConstanstAbstraction;

class ProviderConstant extends ConstanstAbstraction
{
    public const LAPAKGAMING = 'lapakgaming';
    public const VEXAGAME = 'vexagame';
    public const MANUAL = 'manual';

    public const AVAILABLE_PROVIDER = [
        self::LAPAKGAMING => 'LapakGaming',
        self::VEXAGAME => 'Vexagame',
        self::MANUAL => 'Manual'
    ];
}
