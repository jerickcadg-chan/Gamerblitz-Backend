<?php

namespace App\Models;

use App\Traits\WhereByClient;
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
    use WhereByClient;

    const QRIS = 'qris';
    const SALDO = 'saldo';

    protected $fillable = [
        'name', 'admin_fee', 'admin_type', 'slug', 'vendor', 'category', 'client_id'
    ];

    public function getDisplayNameAttribute()
    {
        return strtoupper(\str_replace('_', ' ', $this->name));
    }

    public function getAdminFeeTranslatedAttribute()
    {
        return $this->admin_type == 'percentage' ? (float)$this->admin_fee.'%' : rp_format($this->admin_fee);
    }

    public function allowedFilters(): AllowedFilterList
    {
        return Filter::only(
            Filter::field('vendor', [FilterType::EQUAL]),
        );
    }
}
