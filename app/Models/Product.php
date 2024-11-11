<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\WithPictures;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use WithPictures, SoftDeletes;

    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
    const VOUCHER = 'voucher';

    protected $fillable = [
        'name', 'code', 'category', 'description', 'company', 'how_to_order', 'input_format', 'slug', 'status', 'markup_reseller', 'markup_user'
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
                return "<label class=\"badge badge-success\">Aktif</label>";
                break;

            case self::INACTIVE:
                return "<label class=\"badge badge-danger\">Tidak aktif</label>";
                break;

            default:
                return $this->status;
                break;
        }
    }

    public function getFullSlugAttribute()
    {
        return env('STORE_URL') . '/topup/' . $this->slug;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = \slugify($value);
    }
}
