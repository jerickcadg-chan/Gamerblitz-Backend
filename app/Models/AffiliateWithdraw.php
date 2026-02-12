<?php

namespace App\Models;

use App\Constants\StatusConst;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateWithdraw extends Model
{
    protected $fillable = [
        'affiliate_id',
        'user_id',
        'amount',
        'status',
        'requested_at',
        'processed_at',
        'payout_account_id',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payoutAccount(): BelongsTo
    {
        return $this->belongsTo(PayoutAccount::class, 'payout_account_id');
    }

    public function getStatusRawAttribute(): string
    {
        return match ($this->status) {
            StatusConst::PENDING => '<span class="badge badge-warning">'.ucwords($this->status).'</span>',
            StatusConst::PAID => '<span class="badge badge-success">'.ucwords($this->status).'</span>',
            StatusConst::REJECTED => '<span class="badge badge-danger">'.ucwords($this->status).'</span>',
            default => $this->status,
        };
    }

    // Scopes
    public function scopePending($q) { return $q->where('status', 'pending'); }
    public function scopePaid($q)    { return $q->where('status', 'paid'); }
}
