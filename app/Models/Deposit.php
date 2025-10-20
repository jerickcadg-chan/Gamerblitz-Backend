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
        'admin_fee',
        'total_amount',
        'converted_amount',
        'converted_admin_fee',
        'converted_total_amount',
        'status',
        'expired_at',
        'paid_at',
        'currency_code',
        'converted_currency_code',
        'exchange_rate',
        'payment_url',
        'payment_code',
        'payment_id',
        'additional_informations'
    ];

    protected $casts = [
        'amount' => 'string',
        'admin_fee' => 'string',
        'total_amount' => 'string',
        'converted_amount' => 'string',
        'converted_admin_fee' => 'string',
        'converted_total_amount' => 'string',
    ];

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

    public function getAddInformationFormatAttribute(): ?string
    {
        if (! $this->additional_informations) {
            return null;
        }

        $decoded = $this->additional_informations;

        if (is_array($decoded)) {
            $custAccount = '';
            foreach ($decoded as $key => $value) {
                $custAccount .= "<p>{$key}: {$value}</p>";
            }
            return $custAccount;
        }

        return null;
    }
}
