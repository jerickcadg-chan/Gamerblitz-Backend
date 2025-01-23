<?php

namespace App\Models;

use App\Constants\ProductConstant;
use App\Constants\ProductJoki;
use Illuminate\Database\Eloquent\Model;
use App\Traits\WithPictures;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    protected $fillable = [
        'name',
        'code',
        'category',
        'description',
        'company',
        'how_to_order',
        'input_format',
        'slug',
        'status',
        'markup_reseller',
        'markup_user',
        'product_joki'
    ];

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
        return config('array.store_url') . '/topup/' . $this->slug;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = \slugify($value);
    }

    public function productClient()
    {
        return $this->hasMany(ProductClient::class)
            ->where('client_id', client()?->id);
    }

    public function productCover(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->productClient
                ->first()
                ?->cover
                ?->url ?? $this->default_picture_url ?? asset('images/no-image.png')
        );
    }

    public function productPicture(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->productClient
                ->first()
                ?->picture
                ?->url ?? $this->default_picture_url ?? asset('images/no-image.png')
        );
    }

    public function productCategory(): Attribute
    {
        $category = ProductConstant::getTitle($this->category);
        return Attribute::make(
            get: fn (): string => $this->category == ProductConstant::JOKI ?
                str($category)
                ->append(" ")
                ->append("(")
                ->append(ProductJoki::getTitle($this->product_joki))->append(")") :
                $category
        );
    }
}
