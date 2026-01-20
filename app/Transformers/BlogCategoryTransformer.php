<?php

namespace App\Transformers;

use App\Models\BlogCategory;
use League\Fractal\TransformerAbstract;

class BlogCategoryTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = ['blogs'];

    public function transform(BlogCategory $cat)
    {
        return [
            'id'         => $cat->id,
            'name'       => $cat->name,
            'slug'       => $cat->slug,
            'created_at' => $cat->created_at,
            'updated_at' => $cat->updated_at,
        ];
    }

    public function includeBlogs(BlogCategory $cat)
    {
        return $this->collection($cat->blogs, new BlogTransformer());
    }
}
