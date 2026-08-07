<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Streamer extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'channel_name',
        'platform',
        'channel_url',
        'platform_channel_id',
        'avatar_url',
        'is_live',
        'platform_data_updated_at',
        'commission_rate',
        'discount_rate',
        'balance',
        'total_earnings',
        'status',
    ];
    
    protected $casts = [
        'commission_rate' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'balance' => 'decimal:2',
        'total_earned' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(StreamerHistory::class);
    }

    public function withdraws(): HasMany
    {
        return $this->hasMany(StreamerWithdraw::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public static function findByCode(string $code): ?self
    {
        return static::where('code', strtoupper($code))
            ->where('status', 'active')
            ->first();
    }
}
