<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\WithPictures;

class Slider extends Model
{
    use WithPictures;

    protected $fillable = [
        'name', 'url', 'start_date', 'end_date'
    ];
}
