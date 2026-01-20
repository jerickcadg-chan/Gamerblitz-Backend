<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EcommerceVariantOption extends Model
{
    use HasFactory;

    protected $table = 'ecommerce_variant_options';

    protected $fillable = [
        'ecommerce_product_id',
        'name',
        'position',
    ];

    public function product()
    {
        return $this->belongsTo(EcommerceProduct::class, 'ecommerce_product_id');
    }

    public function values()
    {
        return $this->hasMany(EcommerceProductVariant::class, 'variant_option_id')
            ->orderBy('position');
    }
}