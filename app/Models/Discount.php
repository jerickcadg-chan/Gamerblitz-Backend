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

    public function products(): HasMany
    {
        return $this->hasMany(DiscountProduct::class);
    }

    // TODO: their is no
    public function scopeActive($query)
    {
        $now = now()->format('Y-m-d 00:00:00');

        return $query->where('is_active', 1)
            ->whereRaw('used < maximum')
            ->whereDate('start_date', '<=', $now)
            ->whereDate('end_date', '>=', $now);
    }

    public function setCodeAttribute($value): void
    {
        $this->attributes['code'] = strtoupper($value);
    }

    public function getDiscountAttribute()
    {
        return $this->disc_type == 'percentage' ? $this->nominal . '%' : currency_format($this->nominal);
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

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => '<label class="badge badge-success">Active</label>',
            'inactive' => '<label class="badge badge-danger">Inactive</label>',
            default => $this->status,
        };
    }

    public function getProductTypeDescAttribute()
    {
        switch ($this->product_type) {
            case \App\Models\Discount::ALL:
                return 'For all products';
                break;

            case \App\Models\Discount::PRODUCT_TYPE:
                return 'For selected product';
                break;

            case \App\Models\Discount::PRODUCT_ITEM:
                return 'For selected product item';
                break;

            default:
                $this->product_type;
                break;
        }
    }
}
