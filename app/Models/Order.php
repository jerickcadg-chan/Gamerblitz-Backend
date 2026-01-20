<?php

namespace App\Models;

use App\Constants\StatusConst;
use App\Events\OrderStatusUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
    public const PENDING = 'pending';
    public const SETTLEMENT = 'settlement';
    public const REFUNDED = 'refunded';

    // order status
    public const EXPIRED = 'expired';
    public const CANCELED = 'canceled';
    public const WAITING_PAYMENT = 'waiting-payment';
    public const INPROCESS = 'on-process';
    public const DONE = 'done';

    protected $casts = [
        'additional_informations' => 'array',
        'expired_at' => 'datetime',
    ];

    protected $fillable = [
        'code',
        'user_id',
        'cust_email',
        'cust_phone_number',
        'product_item_id',
        'cust_account',
        'provider',
        'payment_method',
        'status',
        'serial_number',
        'qty',
        'price',
        'capital',
        'turnover',
        'admin_fee',
        'discount_price',
        'total_price',
        'total_income',
        'note',
        'expired_at',
        'currency_code',
        'converted_currency_code',
        'exchange_rate',
        'payment_url',
        'payment_code',
        'payment_id',
        'additional_informations',
        'updated_by'
    ];

    protected $appends = ['cust_account_array'];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->attributes['code'] = Setting::getByKey('base_inv_code') . date('ymd') . strtoupper(substr(uniqid(), -5));
        });

        static::saving(function ($order) {
            $rate = $order->exchange_rate;

            $order->converted_price          = $order->price * $rate;
            $order->converted_capital        = $order->capital * $rate;
            $order->converted_turnover       = $order->turnover * $rate;
            $order->converted_admin_fee      = $order->admin_fee * $rate;
            $order->converted_discount_price = $order->discount_price * $rate;
            $order->converted_total_price    = $order->total_price * $rate;
            $order->converted_total_income   = $order->total_income * $rate;
        });

        static::updating(function ($order) {
            if ($order->isDirty('status') && $order->status == StatusConst::SUCCESS) {
                broadcast(new OrderStatusUpdated($order));
            }
        });
    }

    // <CHANGE> Add ecommerce order relationship
    public function ecommerceOrder(): BelongsTo
    {
        return $this->belongsTo(EcommerceOrder::class);
    }

    public function productItem(): BelongsTo
    {
        return $this->belongsTo(ProductItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }

    public function voucher(): HasOne
    {
        return $this->hasOne(Voucher::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(OrderHistory::class);
    }

    public function scopeSettlement($query)
    {
        return $query->where('payment_status', self::SETTLEMENT);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function getPaymentUrlFullAttribute(): ?string
    {
        return config('app.fe_url').'/'.config('app.fe_invoice_url').'/'.$this->code;
    }

    public function getCustAccountFormatAttribute(): ?string
    {
        if (! $this->cust_account) {
            return null;
        }

        $decoded = json_decode($this->cust_account, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $custAccount = '';
            foreach ($decoded as $key => $value) {
                $custAccount .= "<p>{$key}: {$value}</p>";
            }
            return $custAccount;
        }

        return $this->cust_account;
    }

    public function getAdditionalInformationHtmlAttribute(): ?string
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

    public function getOrderStatusRawAttribute(): string
    {
        return match ($this->status) {
            StatusConst::PENDING => '<span class="badge badge-warning">'.ucwords($this->status).'</span>',
            StatusConst::ON_PROCESS => '<span class="badge badge-info">'.ucwords($this->status).'</span>',
            StatusConst::SUCCESS => '<span class="badge badge-success">'.ucwords($this->status).'</span>',
            StatusConst::DELAY => '<span class="badge badge-primary">'.ucwords($this->status).'</span>',
            StatusConst::EXPIRED, StatusConst::FAILED, StatusConst::REFUNDED => '<span class="badge badge-danger">'.ucwords($this->status).'</span>',
            default => $this->status,
        };
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
