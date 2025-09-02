<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
use IndexZer0\EloquentFiltering\Target\Target;

/**
 * @mixin IdeHelperOrder
 */
class Order extends Model implements IsFilterable
{
    use HasFactory;
    use Filterable;

    // payment status
    const PENDING = 'pending';
    const SETTLEMENT = 'settlement';
    const REFUNDED = 'refunded';

    // order status
    const EXPIRED = 'expired';
    const CANCELED = 'canceled';
    const WAITING_PAYMENT = 'waiting-payment';
    const INPROCESS = 'in-process';
    const DONE = 'done';

    protected $fillable = [
        'code',
        'user_id',
        'cust_email',
        'cust_phone_number',
        'product_item_id',
        'cust_account',
        'payment_method',
        'payment_status',
        'order_status',
        'qty',
        'price',
        'capital',
        'admin_fee',
        'discount_price',
        'total_price',
        'total_income',
        'note',
        'expired_at',
        'currency_code',
        'converted_currency_code',
        'exchange_rate',
    ];

    protected $appends = ['cust_account_array'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->attributes['code'] = 'INV' . date('ymd') . strtoupper(substr(uniqid(), -5));
        });

        static::saving(function ($order) {
            $rate = $order->exchange_rate;

            $order->converted_capital        = $order->capital * $rate;
            $order->converted_admin_fee      = $order->admin_fee * $rate;
            $order->converted_discount_price = $order->discount_price * $rate;
            $order->converted_total_price    = $order->total_price * $rate;
            $order->converted_total_income   = $order->total_income * $rate;
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
                    foreach (json_decode($this->cust_account) as $key => $value) {
                        $custAccount .= "<p>{$key} = {$value}</p>";
                    }
                    return $custAccount;
                }
            }
        }

        return $this->cust_account;
    }

    public function getCustAccountArrayAttribute()
    {
        if ($this->cust_account) {
            $decoded = json_decode($this->cust_account, true);
            if ($decoded) {
                return $decoded;
            }
        }
        return [];
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

    // TODO:
    // date_range, $between
    // code $eq, $like
    // item name $like, $eq
    public function allowedFilters(): AllowedFilterList
    {
        return Filter::only(
            Filter::relation(
                'productItem',
                [FilterType::HAS],
                Filter::only(
                    Filter::relation(
                        'product',
                        [FilterType::HAS],
                        Filter::only(
                            Filter::field(Target::alias('item_name', 'name'), [FilterType::EQUAL, FilterType::LIKE]),
                        )
                    ),
                )
            ),
            Filter::field('code', [FilterType::EQUAL, FilterType::LIKE]),
            Filter::field(Target::alias('date_range', 'created_at'), [FilterType::BETWEEN]),
        );
    }
}
