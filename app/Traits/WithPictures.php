<?php


namespace App\Traits;

use App\Models\Picture;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\DB;

trait WithPictures
{

    public function picture(): MorphOne
    {
        return $this->morphOne(Picture::class, 'pictureable')->latest()->where('type', null)->withDefault();
    }

    public function pictureExist()
    {
        return $this->morphMany(Picture::class, 'pictureable')->where('type', null)->count() > 0;
    }

    public function pictures(): MorphMany
    {
        return $this->morphMany(Picture::class, 'pictureable');
    }

    public function pictures_order(): MorphMany
    {
        return $this->morphMany(Picture::class, 'pictureable')
            ->select(['*', DB::raw('IF(`order` IS NOT NULL, `order`, 99999) `short_order`')])
            ->orderBy('short_order', 'asc');
    }

    public function picture_order(): MorphOne
    {
        return $this->morphOne(Picture::class, 'pictureable')
            ->select(['*', DB::raw('IF(`order` IS NOT NULL, `order`, 99999) `short_order`')])
            ->orderBy('short_order', 'asc')
            ->withDefault();
    }
}
