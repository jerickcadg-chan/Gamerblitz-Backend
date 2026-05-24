<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperProductItemCategory
 */
class ProductItemCategory extends Model
{

    protected $guarded = ["id"];

    protected $fillable = [
        'name', 'product_id'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productItems(): HasMany
    {
        return $this->hasMany(ProductItem::class);
    }

    public function metas(): HasMany
    {
        return $this->hasMany(ProductItemCategoryMeta::class);
    }
}
