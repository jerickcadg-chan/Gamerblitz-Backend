<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\WithPictures;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    const BALANCE = 'balance';
    const QR = 'QR';
    const EWALLET = 'E-Wallet';
    const BANK_VA = 'Bank Virtual Account';
    const RETAIL = 'Retail Outlet';

    protected $fillable = [
        'name',
        'admin_fee',
        'admin_type',
        'slug',
        'vendor',
        'category',
        'picture',
        'currency_code',
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
        );
    }
}
