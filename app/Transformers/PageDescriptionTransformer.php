<?php

namespace App\Transformers;

use App\Models\PageDescription;
use League\Fractal\TransformerAbstract;

class PageDescriptionTransformer extends TransformerAbstract
{   
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(PageDescription $pageDescription)
    {
        return [
            'name'  => $pageDescription->name,
            'slug' => $pageDescription->slug,
            'contents' => $pageDescription->contents
        ];
    }
}
