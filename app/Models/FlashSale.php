<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperFlashSale
 */
class FlashSale extends Model
{
    protected $fillable = [
        'product_item_id',
        'stock',
        'price'
    ];

    public function productItem(): BelongsTo
    {
        return $this->belongsTo(ProductItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function isActive(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->stock > 0
        );
    }
}
