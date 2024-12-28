<?php

namespace App\Models;

use App\Constants\DefaultRole;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperProductItem
 */
class ProductItem extends Model
{
    /** @use HasFactory<\Database\Factories\ProductItemFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'product_id', 'name', 'code', 'stock', 'price', 'price_reseller', 'capital', 'type'
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
        return auth()->user() && auth()->user()->role === DefaultRole::RESELLER
            ? ($this->margin_price_reseller ?? $this->margin_price)
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

                if ($productItemClient = $this->productItemClients->firstWhere('client_id', $client_id)) {
                    return (float) $this->capital + ($this->capital * $productItemClient->margin / 100);
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

    public function marginPriceReseller(): Attribute
    {
        return Attribute::make(
            get: function (): float {
                // TODO: Implement marginPriceReseller() method.
                $client_id = client()?->id;

                if ($productItemClient = $this->productItemClients->firstWhere('client_id', $client_id)) {
                    return (float) $this->capital + ($this->capital * $productItemClient->margin / 100);
                }

                return $this->capital;
            },
        );
    }

    public function order(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
