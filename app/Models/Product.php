<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\WithPictures;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'markup_reseller',
        'markup_user',
        'product_joki',
        'default_picture',
        'default_cover',
        'ordering',
        'status'
    ];
    public function productItems()
    {
        return $this->hasMany(ProductItem::class)->latest();
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::ACTIVE);
    }

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }
    public function getStatusViewAttribute()
    {
        return match ($this->status) {
            'active' => '<label class="badge badge-success">Aktif</label>',
            'inactive' => '<label class="badge badge-danger">Tidak aktif</label>',
            default => '<label class="badge badge-warning">'. $this->status .'</label>',
        };
    }

//    public function getFullSlugAttribute()
//    {
//        return "https://" . str(client()->host)->replaceFirst("admin.", "") . '/topup/' . $this->slug;
//    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = \slugify($value);
    }

    public function productCover(): string
    {
        return $this->default_cover ? asset($this->default_cover) : asset('images/no-image.png');
    }

    public function productPicture(): string
    {
        return $this->default_picture ? asset($this->default_picture) : asset('images/no-image.png');
    }

    public function productItemCategories(): HasMany
    {
        return $this->hasMany(ProductItemCategory::class);
    }
}
