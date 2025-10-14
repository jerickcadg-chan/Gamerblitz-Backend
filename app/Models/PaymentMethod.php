<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\WithPictures;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;

/**
 * @mixin IdeHelperPaymentMethod
 */
class PaymentMethod extends Model implements IsFilterable
{
    /** @use HasFactory<\Database\Factories\PaymentMethodFactory> */
    use HasFactory;
    use WithPictures;
    use Filterable;

    const XENDIT = 'xendit';
    const HITPAY = 'hitpay';
    const BILLPLZ = 'billplz';
    const MPAY = 'mpay';
    const MANUAL = 'manual';
    const BALANCE = 'balance';
    const ALL = 'ALL';

    protected $fillable = [
        'name',
        'admin_fee',
        'admin_type',
        'slug',
        'vendor',
        'category',
        'picture',
        'currency_code',
        'account_number',
        'description',
        'is_raw_description',
        'is_active',
        'type',
        'ordering'
    ];

    public function getDisplayNameAttribute(): string
    {
        return strtoupper(\str_replace('_', ' ', $this->name));
    }

    public function getAdminFeeTranslatedAttribute(): string
    {
        return $this->admin_type == 'percentage' ? (float)$this->admin_fee.'%' : currency_format($this->admin_fee, $this->currency_code);
    }

    public function allowedFilters(): AllowedFilterList
    {
        return Filter::only(
            Filter::field('vendor', [FilterType::EQUAL]),
            Filter::field('type', [FilterType::EQUAL]),
            Filter::field('currency_code', [FilterType::IN, FilterType::EQUAL]),
        );
    }

    public function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function getPictureUrlAttribute(): ?string
    {
        return $this->picture ? Storage::url($this->picture) : null;
    }
}
