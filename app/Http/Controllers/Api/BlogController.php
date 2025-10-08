<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Transformers\BlogCategoryTransformer;
use App\Transformers\BlogTransformer;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::select([
            'id',
            'title',
            'slug',
            'meta_description',
            'thumbnail',
            'blog_category_id',
            'user_id',
            'status',
            'published_at',
            'created_at',
            'updated_at',
            'meta_keyword'
        ])
            ->where('status', 'published')
            ->orderBy('published_at', 'desc');

        return api_status_ok(
            paginateTransformer($blogs, new BlogTransformer(), [], request('limit') ?? 10)
        );
    }

    public function all()
    {
        $blogs = Blog::select([
            'id',
            'title',
            'slug',
            'meta_description',
            'thumbnail',
            'blog_category_id',
            'user_id',
            'status',
            'published_at',
            'created_at',
            'updated_at',
            'meta_keyword'
        ])
            ->where('status', 'published')
            ->orderBy('published_at', 'desc');

        return api_status_ok(transformer($blogs->get(), new BlogTransformer()));
    }

    public function latestPerCategory()
    {
        $categories = BlogCategory::with(['blogs' => function ($q) {
            $q->select([
                'id',
                'title',
                'slug',
                'meta_description',
                'thumbnail',
                'blog_category_id',
                'user_id',
                'status',
                'published_at',
                'created_at',
                'updated_at',
                'meta_keyword'
            ])
                ->where('status', 'published')
                ->orderBy('published_at', 'desc')
                ->limit(5);
        }])->get();

        return api_status_ok(transformer($categories, new BlogCategoryTransformer()));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $blog = Blog::where('slug', $slug)->first();
        if (!$blog) {
            return api_status_warning('Blog not found', 404);
        }
        return api_status_ok(transformer($blog, new BlogTransformer()));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blog $blog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        //
    }
}
