<?php

namespace App\Models;

use App\Traits\WhereByClient;
use Illuminate\Database\Eloquent\Model;
use App\Traits\WithPictures;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * @mixin IdeHelperSlider
 */
class Slider extends Model
{
    /** @use HasFactory<\Database\Factories\SliderFactory> */
    use HasFactory;
    use WithPictures;
    use WhereByClient;

    protected $fillable = [
        'name', 'url', 'start_date', 'end_date'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($slider) {
            if (Auth::check()) {
                $slider->client_id = client()?->id;
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
