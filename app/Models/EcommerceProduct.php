<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EcommerceProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'sale_price',
        'capital_price',
        'stock',
        'image',
        'is_active',
        'is_featured',
        'track_stock',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'capital_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'track_stock' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && !$product->isDirty('slug')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(EcommerceCategory::class, 'category_id');
    }

    public function variantOptions()
    {
        return $this->hasMany(EcommerceVariantOption::class, 'ecommerce_product_id');
    }

    public function variants()
    {
        return $this->hasManyThrough(
            EcommerceProductVariant::class,
            EcommerceVariantOption::class,
            'ecommerce_product_id',
            'variant_option_id',
            'id',
            'id'
        );
    }

    public function logs()
    {
        return $this->hasMany(EcommerceProductLog::class, 'product_id')->latest();
    }
}