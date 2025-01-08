<?php

namespace App\Models;

use App\Traits\WithPictures;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @mixin IdeHelperProductClient
 */
class ProductClient extends Model
{
    /** @use HasFactory<\Database\Factories\ProductClientFactory> */
    use HasFactory;
    use WithPictures;

    public $fillable = [
        'product_id',
        'client_id',
        'is_active',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function cover(): MorphOne
    {
        return $this->morphOne(Picture::class, 'pictureable')->where('type', 'cover')->latest();
    }
}
