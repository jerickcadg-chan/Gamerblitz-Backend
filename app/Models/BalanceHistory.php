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
        'balance_id', 'balanceable_type', 'balanceable_id', 'description', 'amount', 'latest_balance'
    ];

    public function balance(): BelongsTo
    {
        return $this->belongsTo(Balance::class);
    }
}
