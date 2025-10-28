<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\Picture;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PictureService extends Controller
{
    public function upload(UploadedFile $picture, $model, $location, $caption, $type)
    {
        $imageName = substr(uniqid(), -9) . '-' . $picture->getClientOriginalName();

        Storage::disk(config('filesystems.default'))->putFileAs($location, $picture, $imageName);

        $modelPicture = $model->picture()->create([
            'path' => $location,
            'file_name' => $imageName,
            'caption' => $caption,
            'type' => $type,
        ]);

        return $modelPicture;
    }

    public function insert(UploadedFile $picture)
    {
        $imageName = substr(uniqid(), -9) . '-' . $picture->getClientOriginalName();
        $path = Storage::disk('public')->putFileAs('image', $picture, $imageName);

        return $path;
    }

    public function insertFromUrl(string $path, string $url)
    {
        $response = Http::get($url);

        if ($response->failed()) {
            throw new \Exception('Failed to download file from url');
        }

        $pathInfo = pathinfo(parse_url($url, PHP_URL_PATH));
        $extension = $pathInfo['extension'] ?? 'jpg';
        $fileName = Str::random(10) . '.' . $extension;

        Storage::disk('public')->put("$path/{$fileName}", $response->body());

        return "$path/{$fileName}";
    }

    public function delete(Picture $picture)
    {
        Storage::disk(config('filesystems.default'))->delete($picture->path . '/' . $picture->file_name);
        $picture->delete();
    }
}
