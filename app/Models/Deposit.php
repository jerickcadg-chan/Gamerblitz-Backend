<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperDeposit
 */
class Deposit extends Model
{
    protected $fillable = [
        'code',
        'user_id',
        'payment_method_id',
        'amount',
        'unique_code',
        'total_amount',
        'status',
        'expired_at',
        'paid_at',
        'currency_code',
        'converted_currency_code',
        'exchange_rate',
        'payment_url',
        'payment_code',
        'payment_id'
    ];

    protected $casts = [
        'amount' => 'string',
        'unique_code' => 'string',
        'total_amount' => 'string',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::saving(function ($deposit) {
            $rate = $deposit->exchange_rate;

            $deposit->converted_amount       = $deposit->amount * $rate;
            $deposit->converted_unique_code  = $deposit->unique_code * $rate;
            $deposit->converted_total_amount = $deposit->total_amount * $rate;
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function getStatusRawAttribute(): string
    {
        return match ($this->status) {
            'pending' => '<span class="badge badge-primary">'. ucfirst($this->status) .'</span>',
            'paid' => '<span class="badge badge-success">'. ucfirst($this->status) .'</span>',
            'expired' => '<span class="badge badge-danger">'. ucfirst($this->status) .'</span>',
            default => $this->status,
        };
    }
}
