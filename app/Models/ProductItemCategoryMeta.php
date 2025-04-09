<?php

namespace App\Models;

use App\Traits\WithPictures;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductItemCategoryMeta extends Model
{
    use HasFactory;
    use WithPictures;

    protected $guarded = ['id'];

    public function productItemCategory(): BelongsTo
    {
        return $this->belongsTo(ProductItemCategory::class);
    }
}
