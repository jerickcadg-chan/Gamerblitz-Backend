<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperProductItemCategory
 */
class ProductItemCategory extends Model
{
    /** @use HasFactory<\Database\Factories\ProductItemCategoryFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productItems(): HasMany
    {
        return $this->hasMany(ProductItem::class);
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'active');
    }
}
