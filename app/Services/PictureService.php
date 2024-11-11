<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PictureService extends Controller
{
    public function upload($picture, $model, $location, $caption)
    {
        $imageName = substr(uniqid(), -9).'-'.$picture->getClientOriginalName();

        $picture->move($location, $imageName);

        $picture = $model->picture()->create([
            'path' => $location,
            'file_name' => $imageName,
            'caption' => $caption,
        ]);

        return $picture;
    }
}
