<?php

namespace App\Models;

use App\Traits\WithPictures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductItemCategoryMeta extends Model
{
    use WithPictures;

    protected $guarded = ['id'];

    public function productItemCategory(): BelongsTo
    {
        return $this->belongsTo(ProductItemCategory::class);
    }

    public function productItems(): HasMany
    {
        return $this->hasMany(ProductItem::class);
    }
}
