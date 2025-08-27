<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateWithdraw extends Model
{
    protected $fillable = [
        'affiliate_id',
        'user_id',
        'amount',
        'status',
        'method',
        'destination',
        'notes',
        'requested_at',
        'processed_at',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($q) { return $q->where('status', 'pending'); }
    public function scopePaid($q)    { return $q->where('status', 'paid'); }
}
