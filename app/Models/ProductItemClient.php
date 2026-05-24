<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperProductItemClient
 */
class ProductItemClient extends Pivot
{
    /** @use HasFactory<\Database\Factories\ProductItemClientFactory> */
    use HasFactory;

    public $table = 'product_item_client';

    public $fillable = [
        'product_item_id',
        'client_id',
        'margin',
        'reseller_margin',
        'is_active',
    ];

    public function productItem(): BelongsTo
    {
        return $this->belongsTo(ProductItem::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
