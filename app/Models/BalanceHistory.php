<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperBalanceHistory
 */
class BalanceHistory extends Model
{
    protected $fillable = [
        'balance_id',
        'balanceable_type',
        'balanceable_id',
        'description',
        'amount',
        'latest_balance',
        'updated_by',
    ];

    public function balance(): BelongsTo
    {
        return $this->belongsTo(Balance::class);
    }

    public function balanceable()
    {
        return $this->morphTo();
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
