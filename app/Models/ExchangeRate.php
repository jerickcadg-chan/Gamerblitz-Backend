<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = [
        'currency_from',
        'currency_to',
        'rate',
        'effective_at',
    ];
}
