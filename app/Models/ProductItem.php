<?php

namespace App\Models;

use App\Constants\DefaultRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id', 'name', 'code', 'stock', 'price', 'price_reseller', 'capital'
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
}
