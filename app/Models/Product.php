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
        'code',
        'category',
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
        'product_category_id'
    ];

    public function productClientName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->productClient->first()?->name ?? $this->name
        );
    }

    public function productItems()
    {
        return $this->hasMany(ProductItem::class)->latest();
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::ACTIVE);
    }

    public function getStatusViewAttribute()
    {
        if ($this->productClient->first() == null && $this->status == 'active') {
            return '<label class="badge badge-success">Aktif</label>';
        }
        switch ($this->productClient->first()?->is_active) {
            case 1:
                return '<label class="badge badge-success">Aktif</label>';
                break;

            case 0:
                return '<label class="badge badge-danger">Tidak aktif</label>';
                break;

            default:
                return '<label class="badge badge-warning">Klaim!</label>';
                break;
        }
    }

    public function getFullSlugAttribute()
    {
        return "https://" . str(client()->host)->replaceFirst("admin.", "") . '/topup/' . $this->slug;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = \slugify($value);
    }

    public function productClient()
    {
        return $this->hasMany(ProductClient::class, 'product_id', 'id')
            ->where('client_id', client()?->id);
    }

    public function productCover(): Attribute
    {
        return Attribute::make(
            get: fn(): string => $this->productClient
                ->first()
                ?->cover
                ?->url ?? $this->default_cover ?? asset('images/no-image.png')
        );
    }

    public function productPicture(): Attribute
    {
        return Attribute::make(
            get: fn(): string => $this->productClient
                ->first()
                ?->picture
                ?->url ?? $this->default_picture ?? asset('images/no-image.png')
        );
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function category(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->productCategory?->slug ?? 'other'
        );
    }

    public function productItemCategories(): HasMany
    {
        return $this->hasMany(ProductItemCategory::class);
    }
}
