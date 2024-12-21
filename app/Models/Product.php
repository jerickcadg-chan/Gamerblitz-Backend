<?php

namespace App\Models;

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
        'name', 'code', 'category', 'description', 'company', 'how_to_order', 'input_format', 'slug', 'status', 'markup_reseller', 'markup_user',
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
        switch ($this->status) {
            case self::ACTIVE:
                return '<label class="badge badge-success">Aktif</label>';
                break;

            case self::INACTIVE:
                return '<label class="badge badge-danger">Tidak aktif</label>';
                break;

            default:
                return $this->status;
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
                ?->picture
                ?->url ?? asset('images/no-image.png'),
        );
    }
}
