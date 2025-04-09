<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin IdeHelperPicture
 */
class Picture extends Model
{
    /**
    * @use HasFactory<\Database\Factories\PictureFactory>
    */
    use HasFactory;

    protected $appends = ['url'];

    protected $fillable = [
        'path',
        'file_name',
        'caption',
        'is_local',
        'type',
    ];

    public function pictureable()
    {
        return $this->morphTo();
    }

    public function getUrlAttribute()
    {
        if (is_null($this->path)) {
            return null;
        }

        return Storage::disk('s3')->url($this->path.'/'.$this->file_name);
    }

}
