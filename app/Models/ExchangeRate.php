<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * All exchange rate is relative to USD as canonical source
 * example:
 * currency_code=PHP rate=57.07
 * that means 1 USD = 57.07
 */
class ExchangeRate extends Model
{
    protected $fillable = [
        'currency_code',
        'rate',
        'effective_at',
    ];

    public function scopeEffectiveRate(Builder $query, string $currency): void
    {
        $query->where('currency_code', $currency)->where('effective_at', '<=', now())->orderByDesc('effective_at');
    }
}
