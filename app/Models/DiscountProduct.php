<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperDiscountProduct
 */
class DiscountProduct extends Model
{
    protected $table = 'discount_product';

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function productable()
    {
        return $this->morphTo();
    }
}
