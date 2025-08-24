<?php

namespace App\Models;

use App\Constants\DefaultRole;
use App\Constants\ProductItemTypeConstant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class ProductItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'code',
        'stock',
        'price',
        'price_silver',
        'price_gold',
        'price_vip',
        'type',
        // TODO: add capital column -> price from provider
        // TODO: add margin public user
        // TODO: add margin silver
        // TODO: add margin gold
        // TODO: add margin vip
    ];

    protected $appends = [
        'real_price',
        'total_price',
        'capital',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function flashSales(): HasMany
    {
        return $this->hasMany(FlashSale::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->product->name ." ". $this->name;
    }

    public function getDiscountPriceAttribute()
    {
        if ($this->flashSaleProductItem) {
            return $this->real_price - $this->flashSaleProductItem->price;
        }

        $disc = get_active_discount($this->price, $this->product_id, $this->id);

        return $disc['nominal'];
    }

    public function getTotalPriceAttribute()
    {
        return $this->real_price - $this->discount_price;
    }

    public function getRealPriceAttribute()
    {
        return Auth::user() && Auth::user()->role === DefaultRole::RESELLER
            ? ($this->price_reseller ?? $this->margin_price)
            : $this->margin_price;
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class)
            ->where('client_id', client()?->id);
    }

    public function marginPrice()
    {
        return Attribute::make(
            get: function () {
//                $client_id = client()?->id;
//
//                // (100%-1,5%)
//                // 10.000:98,5% = 10.153
//                if ($productItemClient = $this->productItemClients->firstWhere('client_id', $client_id)) {
//                    $realPrice = (100 - $productItemClient->margin);
//                    $actualPrice = $this->capital / ($realPrice / 100);
//
//                    return (float) $actualPrice;
//                }

                return $this->capital;
            },
        );
    }

    public function marginPercentage(): Attribute
    {
        return Attribute::make(
            get: function () {
//                $client_id = client()?->id;
//
//                if ($productItemClient = $this->productItemClients->firstWhere('client_id', $client_id)) {
//                    return (float) $productItemClient->margin;
//                }

                return 0;
            },
        );
    }

    public function marginReseller(): Attribute
    {
        return Attribute::make(
            get: function () {
//                $client_id = client()?->id;
//
//                if ($productItemClient = $this->productItemClients->firstWhere('client_id', $client_id)) {
//                    return (float) $productItemClient->reseller_margin;
//                }

                return 0;
            },
        );
    }

    public function marginPriceReseller(): Attribute
    {
        return Attribute::make(
            get: function () {
//                $client_id = client()?->id;
//                if ($this->type == ProductItemTypeConstant::ACCOUNT) {
//                    return $this->price;
//                }
//                if ($productItemClient = $this->productItemClients->firstWhere('client_id', $client_id)) {
//                    $realPrice = (100 - $productItemClient->reseller_margin);
//                    $actualPrice = $this->capital / ($realPrice / 100);
//
//                    return (float) $actualPrice;
//                }

                return $this->capital;
            },
        );
    }

//    public function capital(): Attribute
//    {
//        return Attribute::get(
//            get: function () {
//                $level = client()->level ?? null;
//                if (! $level) {
//                    return $this->price_silver;
//                }
//                if ($this->type == ProductItemTypeConstant::ACCOUNT) {
//                    return $this->price;
//                }
//                $capital = match ($level) {
//                    UserLevel::SILVER => $this->price_silver,
//                    UserLevel::GOLD => $this->price_gold,
//                    UserLevel::PLATINUM => $this->price_vip,
//                };
//
//                return $capital;
//            }
//        );
//    }

    public function order(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function flashSaleProductItem(): HasOne
    {
        return $this->hasOne(FlashSaleProductItem::class)
            ->whereHas('flashSale', function ($query) {
                $query->active();
            });
    }

    public function productItemCategory(): BelongsTo
    {
        return $this->belongsTo(ProductItemCategory::class);
    }
}
