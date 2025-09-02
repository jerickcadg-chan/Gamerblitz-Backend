<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

/**
 * @mixin IdeHelperDiscount
 */
class Discount extends Model
{
    /** @use HasFactory<\Database\Factories\DiscountFactory> */
    use HasFactory;

    const ALL = 'all';
    const PRODUCT_TYPE = 'product_type';
    const PRODUCT_ITEM = 'product_item';

    protected $fillable = [
        'name',
        'code',
        'description',
        'nominal',
        'disc_type',
        'product_type',
        'start_date',
        'end_date',
        'is_active',
        'maximum',
        'used'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($discount) {
            if (Auth::check()) {
                $discount->client_id = client()?->id;
            }
        });
    }

    public function products(): HasMany
    {
        return $this->hasMany(DiscountProduct::class);
    }

    // TODO: ther is no
    public function scopeActive($query)
    {
        $now = now()->format('Y-m-d 00:00:00');

        return $query->where('is_active', 1)
            ->whereRaw('used < maximum')
            ->whereDate('start_date', '<=', $now)
            ->whereDate('end_date', '>=', $now);
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }

    public function getDiscountAttribute()
    {
        return $this->disc_type == 'percentage' ? $this->nominal . '%' : rp_format($this->nominal);
    }

    public function getStatusAttribute(): string
    {
        $now = now()->format('Y-m-d 00:00:00');

        if ($this->is_active == 0) {
            return 'inactive';
        }

        if ($this->used >= $this->maximum) {
            return 'inactive';
        }

        if ($now >= $this->start_date && $now <= $this->end_date) {
            return 'active';
        } else {
            return 'inactive';
        }
    }

    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 'active':
                return '<label class="badge badge-success">Aktif</label>';
                break;

            case 'inactive':
                return '<label class="badge badge-danger">Tidak Aktif</label>';
                break;

            default:
                return $this->status;
                break;
        }
    }

    public function getProductTypeDescAttribute()
    {
        switch ($this->product_type) {
            case \App\Models\Discount::ALL:
                return 'Berlaku untuk semua produk';
                break;

            case \App\Models\Discount::PRODUCT_TYPE:
                return 'Berlaku untuk produk tertentu';
                break;

            case \App\Models\Discount::PRODUCT_ITEM:
                return 'Berlaku untuk produk item tertentu';
                break;

            default:
                $this->product_type;
                break;
        }
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
