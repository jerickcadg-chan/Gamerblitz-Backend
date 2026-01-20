<?php

namespace App\Constants;

use Sheenazien8\Konstantiq\ConstanstAbstraction;

class PlatformConstant extends ConstanstAbstraction
{
    public const B2B = 'B2B';
    public const B2C = 'B2C';

    public const AVAILABLE_PLATFORM = [
        self::B2B => 'B2B (API)',
        self::B2C => 'B2C',
    ];
}
