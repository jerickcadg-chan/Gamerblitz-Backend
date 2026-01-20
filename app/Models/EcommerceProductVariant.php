<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'variant_option_id',
        'name',
        'price',
        'sale_price',
        'capital_price',
        'stock',
        'image',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'capital_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function option()
    {
        return $this->belongsTo(EcommerceVariantOption::class, 'variant_option_id');
    }
}