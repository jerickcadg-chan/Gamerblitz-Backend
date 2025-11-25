<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PageDescription;
use App\Transformers\PageDescriptionTransformer;
use Illuminate\Http\Request;

class PageDescriptionController extends Controller
{
    public function index()
    {
        $pageDescriptions = PageDescription::with('contents')->latest()->get();
        return \api_status_ok(transformer($pageDescriptions, PageDescriptionTransformer::class));
    }

    public function show($slug)
    {
        $pageDescription = PageDescription::with('contents')
            ->where('slug', $slug)
            ->firstOrFail();

        return \api_status_ok(transformer($pageDescription, PageDescriptionTransformer::class));
    }
}
