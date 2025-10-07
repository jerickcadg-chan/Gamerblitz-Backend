<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AffiliateHistory extends Model
{
    protected $fillable = [
        'affiliate_id',
        'affiliateable_type',
        'affiliateable_id',
        'amount',
        'amount_before'
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function affiliateable(): MorphTo
    {
        return $this->morphTo();
    }
}
