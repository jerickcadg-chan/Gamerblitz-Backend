<?php

namespace App\Models;

use App\Traits\WithPictures;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
