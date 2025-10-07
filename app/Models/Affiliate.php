<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Affiliate extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'status',
        'balance',
        'affiliateable_type',
        'affiliateable_id',
        'amount'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function affiliateHistories(): MorphMany
    {
        return $this->morphMany(AffiliateHistory::class, 'affiliate');
    }
}
