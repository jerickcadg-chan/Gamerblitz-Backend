<?php

use Illuminate\Http\UploadedFile;

if (!function_exists('insert_picture')) {
    function insert_picture(UploadedFile $picture, $model, $caption = null, $base64 = false)
    {
        $service = new \App\Services\PictureService();
        $folder = strtolower(class_basename($model));
        $location = 'img/' . $folder;

        if ($base64 == true) {
            $pos  = strpos($picture, ';');
            $type = explode(':', substr($picture, 0, $pos))[1];

            $extension = explode('/', $type)[1];
            if ($extension == 'jpeg') $extension = 'jpg';

            $name = $picture->getClientOriginalName() . '-' . substr(uniqid(), -9);

            $path = $location . '/' . $name;

            $service->save_base64_image($picture, $path);

            $picture = $model->picture()->create([
                'path' => $location,
                'file_name' => $name . '.' . $extension,
                'caption' => $caption,
            ]);

            return $picture;
        }

        return $service->upload($picture, $model, $location, $caption);
    }
}


if (!function_exists('insert_pictures')) {
    function insert_pictures($pictures, $model)
    {
        $uploaded = collect();

        foreach ($pictures as $picture) {
            $pic = insert_picture($picture, $model);
            $uploaded->push($pic);
        }

        return $uploaded;
    }
}


if (!function_exists('get_picture_html')) {
    function get_picture_html($url, $class = null, $height = null)
    {
        return '<img src="' . $url . '"
                     onerror="this.onerror=null;this.src=\' ' . asset('images/404.jpg') . ' \';"
                     class="' . $class . '"
                     width=' . $height . '>';
    }
}

if (!function_exists('delete_picture')) {
    function delete_picture($picture)
    {
        $service = new \App\Services\PictureService();
        $service->delete($picture);
    }
}
