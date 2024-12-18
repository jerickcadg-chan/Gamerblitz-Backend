<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * @mixin IdeHelperOrder
 */
class Order extends Model
{
    use HasFactory;

    const PENDING = 'pending';
    const SETTLEMENT = 'settlement';
    const REFUNDED = 'refunded';
    const EXPIRED = 'expired';
    const CANCELED = 'canceled';
    const WAITING_PAYMENT = 'waiting-payment';
    const INPROCESS = 'in-process';
    const DONE = 'done';

    protected $fillable = [
        'code', 'user_id', 'cust_email', 'cust_phone_number', 'product_item_id', 'cust_account', 'payment_method',
        'payment_status', 'order_status', 'qty', 'price', 'admin_fee', 'total_price', 'total_income', 'note',
        'expired_at', 'bangjeff_invoice'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model)
        {
            $model->attributes['code'] = 'KY'.date('ymd').strtoupper(substr(uniqid(), -5));
        });
    }

    public function productItem()
    {
        return $this->belongsTo(ProductItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function voucher()
    {
        return $this->hasOne(Voucher::class);
    }

    public function histories()
    {
        return $this->hasMany(OrderHistory::class);
    }

    public function scopeSettlement($query)
    {
        return $query->where('payment_status', self::SETTLEMENT);
    }

    public function getPaymentUrlFullAttribute()
    {
        $paymentMethod = PaymentMethod::where('name', $this->payment_method)->first();

        if (empty($paymentMethod)) {
            return null;
        }

        return $this->payment_url . $paymentMethod->slug;
    }

    public function getSettlementDateAttribute()
    {
        $date = $this->histories()->where('status', self::SETTLEMENT)
            ->where('type', 'payment')->first();

        return $date ? $date->created_at : null;
    }

    public function getCustAccountFormatAttribute()
    {
        if ($this->cust_account) {
            if (preg_match('/#/', $this->cust_account)) {
                return $this->cust_account;
            } else {
                if (!json_decode($this->cust_account)) {
                    return $this->cust_account;
                } else {
                    $custAccount = '';
                    foreach (json_decode($this->cust_account) as $account) {
                        $custAccount .= "<p>{$account->name} = {$account->value}</p>";
                    }
                    return $custAccount;
                }
            }
        }

        return $this->cust_account;
    }

    public function getPaymentStatusRawAttribute()
    {
        switch ($this->payment_status) {
            case 'pending':
                return '<span class="text-warning">Menunggu Pembayaran</span>';

            case 'settlement':
                return '<span class="text-success">Lunas</span>';

            case 'refunded':
                return '<span class="text-danger">Dikembalikan</span>';

            default:
                return $this->payment_status;
        }
    }

    public function getOrderStatusRawAttribute()
    {
        switch ($this->order_status) {
            case 'waiting-payment':
                return '<span class="text-warning">Menunggu Pembayaran</span>';

            case 'in-process':
                return '<span class="text-warning">Dalam Proses</span>';

            case 'done':
                return '<span class="text-success">Selesai</span>';

            case 'expired':
                return '<span class="text-danger">Kadaluarsa</span>';

            case 'canceled':
                return '<span class="text-danger">Dibatalkan</span>';

            default:
                return $this->order_status;
        }
    }

    public function getPaymentStatusTranslatedAttribute()
    {
        switch ($this->payment_status) {
            case 'pending':
                return 'Belum Dibayar';
                break;

            case 'settlement':
                return 'Lunas';
                break;

            case 'refunded':
                return 'Dikembalikan';
                break;

            default:
                return $this->payment_status;
                break;
        }
    }

    public function getOrderStatusTranslatedAttribute()
    {
        switch ($this->order_status) {
            case 'waiting-payment':
                return 'Menunggu Pembayaran';
                break;

            case 'in-process':
                return 'Dalam Proses';
                break;

            case 'done':
                return 'Selesai';
                break;

            case 'expired':
                return 'Kadaluarsa';
                break;

            case 'canceled':
                return 'Dibatalkan';
                break;

            default:
                return $this->payment_status;
                break;
        }
    }
}
