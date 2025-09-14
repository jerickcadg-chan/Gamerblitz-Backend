<?php

namespace App\Transformers;

use App\Models\BlogCategory;
use League\Fractal\TransformerAbstract;

class BlogCategoryTransformer extends TransformerAbstract
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
    public function transform(BlogCategory $cat)
    {
        return $cat->toArray();
    }
}
