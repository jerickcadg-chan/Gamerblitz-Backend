<?php

namespace App\Models;

use App\Traits\WhereByClient;
use App\Traits\WithPictures;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperAccount
 */
class Account extends Model
{
    /** @use HasFactory<\Database\Factories\AccountFactory> */
    use HasFactory;
    use WhereByClient;
    use WithPictures;

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
            ? $this->discount_amount . '%'
            : rp_format($this->discount_amount)
        );
    }
}

