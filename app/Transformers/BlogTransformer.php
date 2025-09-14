<?php

namespace App\Transformers;

use App\Models\Blog;
use Illuminate\Support\Facades\Storage;
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
        $arr['thumbnail_url'] = Storage::url($blog->thumbnail);
        return $arr;
    }
}
