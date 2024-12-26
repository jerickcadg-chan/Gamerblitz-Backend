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

    public function fullSlug(): Attribute
    {
        return Attribute::make(
            get: fn () => route('account.show', $this->id)
        );
    }
}

