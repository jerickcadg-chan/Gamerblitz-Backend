<?php

namespace App\Models;

use App\Constants\DefaultRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
/* use Illuminate\Database\Eloquent\Relations\HasOne; */
use Illuminate\Support\Facades\Auth;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;

class ProductItem extends Model implements IsFilterable
{
    use HasFactory, Filterable;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_EMPTY = 'empty';
    public const STATUS_NON_ACTIVE = 'non-active';
    public const STATUS_TROUBLE = 'trouble';

    protected $fillable = [
        'product_id',
        'code',
        'name',
        'capital',
        'stock',
        'margin', // margin public
        'margin_silver',
        'margin_gold',
        'margin_vip',
        'product_item_category_id',
        'country_code',
        'status',
    ];

    protected $appends = [
        'margin_price_public',
        'margin_price_silver',
        'margin_price_gold',
        'margin_price_vip',
        // computed based on user tier
        'real_price',
        'total_price',
        'margin_price',
        'margin_percentage',
    ];

    protected $casts = [
        'sync_at' => 'datetime',
    ];

    public function getFullNameAttribute(): string
    {
        return $this->product->name ." ". $this->name;
    }

    public function getDiscountPriceAttribute()
    {
        if ($this->flashSales && $this->capital < $this->flash_sale_price) {
            return $this->real_price - $this->flash_sale_price;
        }

        return 0;
    }

    public function getTotalPriceAttribute()
    {
        return $this->real_price - $this->discount_price;
    }

    public function getRealPriceAttribute()
    {
        return $this->margin_price;
    }

    public function getFlashSalePriceAttribute()
    {
        return $this->flashSales()->active()->first()->price;
    }

    /**
     * computed based on user role
     *
     */
    public function marginPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => (float) (
                $this->capital / ((100 - $this->margin_percentage) / 100)
            ),
        );
    }

    /**
     * computed based on user role
     *
     */
    public function marginPercentage(): Attribute
    {
        return Attribute::make(
            get: function () {
                $user = Auth::user();

                if ($user) {
                    return match ($user->reseller_level) {
                        DefaultRole::RESELLER_SILVER => $this->margin_silver,
                        DefaultRole::RESELLER_GOLD   => $this->margin_gold,
                        DefaultRole::RESELLER_VIP    => $this->margin_vip,
                        default                      => $this->margin,
                    };
                }

                return $this->margin;
            },
        );
    }

    public function marginPricePublic(): Attribute
    {
        return Attribute::make(
            get: fn () => (float) (
                $this->capital / ((100 - $this->margin) / 100)
            ),
        );
    }

    public function marginPriceSilver(): Attribute
    {
        return Attribute::make(
            get: fn () => (float) (
                $this->capital / ((100 - $this->margin_silver) / 100)
            ),
        );
    }

    public function marginPriceGold(): Attribute
    {
        return Attribute::make(
            get: fn () => (float) (
                $this->capital / ((100 - $this->margin_gold) / 100)
            ),
        );
    }

    public function marginPriceVip(): Attribute
    {
        return Attribute::make(
            get: fn () => (float) (
                $this->capital / ((100 - $this->margin_vip) / 100)
            ),
        );
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function flashSales(): HasMany
    {
        return $this->hasMany(FlashSale::class);
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }

    public function order(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @param Builder<Model> $query
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function productItemCategoryMeta(): BelongsTo
    {
        return $this->belongsTo(ProductItemCategoryMeta::class);
    }
}
