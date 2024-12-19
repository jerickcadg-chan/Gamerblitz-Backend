<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperDeposit
 */
class Deposit extends Model
{
    protected $fillable = [
        'code', 'user_id', 'payment_method_id', 'amount', 'unique_code', 'total_amount', 'status', 'expired_at', 'paid_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function getStatusRawAttribute()
    {
        switch ($this->status) {
            case 'pending':
                return '<span class="badge badge-primary">Menunggu Pembayaran</span>';

            case 'paid':
                return '<span class="badge badge-success">Lunas</span>';

            case 'expired':
                return '<span class="badge badge-danger">Kadaluarsa</span>';

            default:
                return $this->status;
        }
    }

    public function getStatusTranslatedAttribute()
    {
        switch ($this->status) {
            case 'pending':
                return 'Menunggu Pembayaran';

            case 'paid':
                return 'Lunas';

            case 'expired':
                return 'Kadaluarsa';

            default:
                return $this->status;
        }
    }
}
