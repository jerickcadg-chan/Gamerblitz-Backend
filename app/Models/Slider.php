<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\WithPictures;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperSlider
 */
class Slider extends Model
{
    /** @use HasFactory<\Database\Factories\SliderFactory> */
    use HasFactory;
    use WithPictures;

    protected $fillable = [
        'name', 'url', 'start_date', 'end_date'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
