<?php

namespace App\Constants;

use Sheenazien8\Konstantiq\ConstanstAbstraction;

class StatusConst extends ConstanstAbstraction
{
    const PENDING = 'pending';
    const PAID = 'paid';
    const EXPIRED = 'expired';
    const SUCCESS = 'success';
    const FAILED = 'failed';
    const ON_PROCESS = 'on-process';
    const REFUNDED = 'refunded';
    const DELAY = 'delay';
}
