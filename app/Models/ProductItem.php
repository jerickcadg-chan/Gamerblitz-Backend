<?php

namespace App\Models;

use App\Constants\DefaultRole;
use App\Constants\ProductItemTypeConstant;
use App\Constants\UserLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;

/**
 * @mixin IdeHelperProductItem
 */
class ProductItem extends Model implements IsFilterable
{
    /** @use HasFactory<\Database\Factories\ProductItemFactory> */
    use HasFactory;
    use SoftDeletes;
    use Filterable;

    protected $fillable = [
        'product_id',
        'name',
        'code',
        'stock',
        'price',
        'capital_silver',
        'capital_gold',
        'capital_platinum',
        'capital_diamond',
        'type'
    ];

    protected $appends = [
        'real_price',
        'total_price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getDiscountPriceAttribute()
    {
        $disc = get_active_discount($this->price, $this->product_id, $this->id);

        return $disc['nominal'];
    }

    public function getTotalPriceAttribute()
    {
        return $this->real_price - $this->discountPrice;
    }

    public function getRealPriceAttribute()
    {
        return Auth::user() && Auth::user()->role === DefaultRole::RESELLER
            ? ($this->price_reseller ?? $this->margin_price)
            : $this->margin_price;
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, ProductItemClient::class);
    }

    public function productItemClients()
    {
        return $this->hasMany(ProductItemClient::class)
            ->when(client()?->id, function ($query) {
                $query->where('client_id', client()?->id);
            });
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class)
            ->where('client_id', client()?->id);
    }

    public function marginPrice(): Attribute
    {
        return Attribute::make(
            get: function (): float {
                $client_id = client()?->id;

                // (100%-1,5%)
                // 10.000:98,5% = 10.153
                if ($productItemClient = $this->productItemClients->firstWhere('client_id', $client_id)) {
                    $realPrice = (100 - $productItemClient->margin);
                    $actualPrice = $this->capital / ($realPrice / 100);

                    return (float) $actualPrice;
                }

                return $this->capital;
            },
        );
    }

    public function marginPercentage(): Attribute
    {
        return Attribute::make(
            get: function (): float {
                $client_id = client()?->id;

                if ($productItemClient = $this->productItemClients->firstWhere('client_id', $client_id)) {
                    return (float)$productItemClient->margin;
                }

                return 0;
            },
        );
    }

    public function marginReseller(): Attribute
    {
        return Attribute::make(
            get: function (): float {
                $client_id = client()?->id;

                if ($productItemClient = $this->productItemClients->firstWhere('client_id', $client_id)) {
                    return (float)$productItemClient->reseller_margin;
                }

                return 0;
            },
        );
    }

    public function marginPriceReseller(): Attribute
    {
        return Attribute::make(
            get: function (): float {
                $client_id = client()?->id;
                if ($this->type == ProductItemTypeConstant::ACCOUNT) {
                    return $this->price;
                }
                if ($productItemClient = $this->productItemClients->firstWhere('client_id', $client_id)) {
                    $realPrice = (100 - $productItemClient->reseller_margin);
                    $actualPrice = $this->capital / ($realPrice / 100);

                    return (float) $actualPrice;
                }

                return $this->capital;
            },
        );
    }

    public function capital(): Attribute
    {
        return Attribute::get(
            get: function () {
                $level = client()->level;
                if ($this->type == ProductItemTypeConstant::ACCOUNT) {
                    return $this->price;
                }
                $capital = match ($level) {
                    UserLevel::SILVER => $this->capital_silver,
                    UserLevel::GOLD => $this->capital_gold,
                    UserLevel::PLATINUM => $this->capital_platinum,
                    UserLevel::DIAMOND => $this->capital_diamond,
                };

                return $capital;
            }
        );
    }

    public function order(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'active');
    }

    public function allowedFilters(): AllowedFilterList
    {
        return Filter::only(
            Filter::field('name', [FilterType::LIKE, FilterType::EQUAL]),
            Filter::field('code', [FilterType::LIKE, FilterType::EQUAL]),
            Filter::field('type', [FilterType::EQUAL]),
            Filter::field('product_item_category_id', [FilterType::EQUAL]),
        );
    }
}
