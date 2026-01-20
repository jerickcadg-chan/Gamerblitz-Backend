<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperBalance
 */
class Balance extends Model
{
    /**
    * @use HasFactory<\Database\Factories\BalanceFactory>
    */
    use HasFactory;

    protected $fillable = [
        'user_id', 'amount',
        'balanceable_type'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(BalanceHistory::class);
    }
}
