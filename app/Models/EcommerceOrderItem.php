<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceOrderItem extends Model
{
    use HasFactory;

protected $fillable = [
    'order_id',
    'product_id',
    'variant_id',
    'product_name',
    'variant_name',
    'price',
    'capital_price',
    'quantity',
    'subtotal',
];

protected $casts = [
    'price' => 'decimal:2',
    'capital_price' => 'decimal:2',
    'subtotal' => 'decimal:2',
];

    public function order()
    {
        return $this->belongsTo(EcommerceOrder::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(EcommerceProduct::class, 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(EcommerceProductVariant::class, 'variant_id');
    }
}