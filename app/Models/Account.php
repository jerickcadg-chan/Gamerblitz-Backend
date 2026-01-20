<?php

namespace App\Models;

use App\Traits\WithPictures;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;

/**
 * @mixin IdeHelperAccount
 */
class Account extends Model implements IsFilterable
{
    /** @use HasFactory<\Database\Factories\AccountFactory> */
    use HasFactory;
    use WithPictures;
    use Filterable;

    public $fillable = [
        'title',
        'description',
        'code',
        'winrate',
        'skin',
        'heroes',
        'information',
        'discount_type',
        'discount_amount',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (Account $model) {
            $model->slug = str($model->title)->slug()->append("-", str($model->code)->slug());
        });
    }

    public function productItem()
    {
        return $this->belongsTo(ProductItem::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function price(): Attribute
    {
        $price = 0;
        if ($this->discount_type != null) {
            if ($this->discount_type == 'percentage') {
                $price = $this->productItem?->price - ($this->productItem?->price * (float) $this->discount_amount / 100);
            } else {
                $price = $this->productItem?->price - $this->discount_amount;
            }
        } else {
            $price = $this->productItem?->price;
        }

        return Attribute::make(
            get: fn () => $price,
        );
    }

    public function fullSlug(): Attribute
    {
        return Attribute::make(
            get: fn () => route('account.show', $this->id)
        );
    }

    public function discount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->discount_type == 'percentage'
                ? ((float) $this->discount_amount) . '%'
                : currency_format($this->discount_amount)
        );
    }

    public function status(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->productItem->order->isNotEmpty()
        );
    }

    public function statusView(): Attribute
    {
        return Attribute::make(
            get: function () {
                $status = $this->status;
                return $status ? 'Terjual' : 'Tersedia';
            }
        );
    }

    public function allowedFilters(): AllowedFilterList
    {
        return Filter::only(
            Filter::relation(
                'productItem',
                [FilterType::HAS],
                Filter::only(
                    Filter::field('price', [FilterType::GREATER_THAN_EQUAL_TO]),
                    Filter::field('price', [FilterType::LESS_THAN_EQUAL_TO]),
                )
            ),
            Filter::field('heroes', [FilterType::EQUAL, FilterType::GREATER_THAN_EQUAL_TO]),
            Filter::field('skin', [FilterType::EQUAL, FilterType::GREATER_THAN_EQUAL_TO]),
            Filter::field('winrate', [FilterType::GREATER_THAN_EQUAL_TO]),
        );
    }
}
