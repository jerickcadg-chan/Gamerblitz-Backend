<?php

namespace App\Models;

use App\Traits\WhereByClient;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperFlashSale
 */
class FlashSale extends Model
{
    /** @use HasFactory<\Database\Factories\FlashSaleFactory> */
    use HasFactory, WhereByClient;

    protected $fillable = [
        'client_id',
        'name',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(FlashSaleProductItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function isActive(): Attribute
    {
        return Attribute::make(
            get: fn () => now()->between($this->start_date, $this->end_date)
        );
    }

    public function statusView(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_active ? '<label class="badge badge-success">Aktif</label>' : '<label class="badge badge-danger">Tidak aktif</label>'
        );
    }
}
