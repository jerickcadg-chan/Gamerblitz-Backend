<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    protected $fillable = [
        'path',
        'file_name',
        'caption',
        'is_local',
    ];

    public function pictureable()
    {
        return $this->morphTo();
    }

    public function getUrlAttribute()
    {
        if (is_null($this->path)) {
            return asset('build/img/logo_watermark.jpg');
        }

        return asset($this->path.'/'.$this->file_name);

    }
}
