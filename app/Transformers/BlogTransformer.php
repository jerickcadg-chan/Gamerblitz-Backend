<?php

namespace App\Transformers;

use App\Models\Blog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class BlogTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Blog $blog)
    {
        $arr = $blog->toArray();

        // safe excerpt: strip all HTML first
        if (!empty($blog->content)) {
            $plain = strip_tags($blog->content);
            $arr['excerpt'] = Str::limit($plain, 200); // 200 chars max
        }

        $arr['thumbnail_url'] = $blog->thumbnail_url;
        $arr['category'] = $blog->category->name;
        $arr['tags'] = $blog->tags->pluck('name');
        $arr['author'] = $blog->author->name;

        return $arr;
    }
}
