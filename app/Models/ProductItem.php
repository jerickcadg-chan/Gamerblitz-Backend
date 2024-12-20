<?php

namespace App\Models;

use App\Constants\DefaultRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'product_id', 'name', 'code', 'stock', 'price', 'price_reseller', 'capital'
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
            ? ($this->price_reseller ?? $this->price)
            : $this->price;
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, ProductItemClient::class);
    }
}
