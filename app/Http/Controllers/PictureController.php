<?php

namespace App\Http\Controllers;

use App\Services\PictureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PictureController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|file'
        ]);

        $path = (new PictureService())->insert($request->file('image'));

        return response()->json([
            'url' => Storage::url($path)
        ]);
    }
}
