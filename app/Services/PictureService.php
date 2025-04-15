<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\Picture;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PictureService extends Controller
{
    public function upload(UploadedFile $picture, $model, $location, $caption, $type)
    {
        $imageName = substr(uniqid(), -9).'-'.$picture->getClientOriginalName();

        Storage::disk(config('filesystems.default'))->putFileAs($location, $picture, $imageName);

        $modelPicture = $model->picture()->create([
            'path' => $location,
            'file_name' => $imageName,
            'caption' => $caption,
            'type' => $type,
        ]);

        return $modelPicture;
    }

    public function delete(Picture $picture)
    {
        Storage::disk(config('filesystems.default'))->delete($picture->path . '/' . $picture->file_name);
        $picture->delete();
    }
}
