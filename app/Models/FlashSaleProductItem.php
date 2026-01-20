<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperFlashSaleProductItem
 */
class FlashSaleProductItem extends Model
{
    /** @use HasFactory<\Database\Factories\FlashSaleProductItemFactory> */
    use HasFactory;

    protected $fillable = [
        'flash_sale_id',
        'product_item_id',
        'price',
        'stock',
    ];

    public function flashSale(): BelongsTo
    {
        return $this->belongsTo(FlashSale::class);
    }

    public function productItem(): BelongsTo
    {
        return $this->belongsTo(ProductItem::class);
    }
}
