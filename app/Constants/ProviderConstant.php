<?php

namespace App\Constants;

use Sheenazien8\Konstantiq\ConstanstAbstraction;

class ProviderConstant extends ConstanstAbstraction
{
    public const LAPAKGAMING = 'lapakgaming';
    public const VEXAGAME = 'vexagame';
    public const DYNASTY_DGS = 'dynasty_dgs';
    public const WHITELABEL = 'whitelabel';
    public const MANUAL = 'manual';

    public const AVAILABLE_PROVIDER = [
        self::LAPAKGAMING => 'LapakGaming',
        self::VEXAGAME => 'Vexagame',
        self::DYNASTY_DGS => 'Dynasty DGS',
        self::WHITELABEL => 'Whitelabel',
        self::MANUAL => 'Manual'
    ];

    /**
     * @return array
     */
    public static function providers(): array
    {
        return array_replace(
            self::AVAILABLE_PROVIDER,
            [
                self::WHITELABEL => env('PROVIDER_WHITELABEL', 'whitelabel'),
            ]
        );
    }
}
