<?php


namespace App\Traits;

use App\Models\Picture;
use Illuminate\Support\Facades\DB;

trait WithPictures
{

    public function picture()
    {
        return $this->morphOne(Picture::class, 'pictureable')->latest()->withDefault();
    }

    public function pictureExist()
    {
        return $this->morphMany(Picture::class, 'pictureable')->count() > 0;
    }

    public function pictures()
    {
        return $this->morphMany(Picture::class, 'pictureable');
    }

    public function pictures_order()
    {
        return $this->morphMany(Picture::class, 'pictureable')
            ->select(['*', DB::raw('IF(`order` IS NOT NULL, `order`, 99999) `short_order`')])
            ->orderBy('short_order', 'asc');
    }

    public function picture_order()
    {
        return $this->morphOne(Picture::class, 'pictureable')
            ->select(['*', DB::raw('IF(`order` IS NOT NULL, `order`, 99999) `short_order`')])
            ->orderBy('short_order', 'asc')
            ->withDefault();
    }
}
