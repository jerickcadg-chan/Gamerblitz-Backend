<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\WithPictures;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin IdeHelperProduct
 */
class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, SoftDeletes, WithPictures;

    const ACTIVE = 'active';

    const INACTIVE = 'inactive';

    const VOUCHER = 'voucher';

    const NOT_VISIBLE = 'not_visible';

    protected $fillable = [
        'name',
        'code',
        'product_category_id',
        'description',
        'company',
        'how_to_order',
        'input_format',
        'slug',
        'markup_reseller_silver',
        'markup_reseller_gold',
        'markup_reseller_vip',
        'markup_user',
        'product_joki',
        'default_picture',
        'default_cover',
        'ordering',
        'status',
        'provider',
        'provider_code',
        'provider_country',
        'check_uid',
        'meta_title',
        'meta_keyword',
        'meta_description',
        'is_raw_description'
    ];

    protected $attributes = [
        'provider' => '',
        'provider_code' => '',
        'provider_country' => '',
    ];

    public function productItems(): HasMany
    {
        return $this->hasMany(ProductItem::class)->latest();
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::ACTIVE);
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }
    public function getStatusViewAttribute(): string
    {
        return match ($this->status) {
            'active' => '<label class="badge badge-success">Active</label>',
            'inactive' => '<label class="badge badge-danger">Inactive</label>',
            default => '<label class="badge badge-warning">'. $this->status .'</label>',
        };
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = \slugify($value);
    }

    public function getProductCoverAttribute(): ?string
    {
        return $this->default_cover ? Storage::url($this->default_cover) : null;
    }

    public function getProductPictureAttribute(): string
    {
        return $this->default_picture ? Storage::url($this->default_picture) : asset('images/no-image.png');
    }

    public function productItemCategories(): HasMany
    {
        return $this->hasMany(ProductItemCategory::class);
    }
}
