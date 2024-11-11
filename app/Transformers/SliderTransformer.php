<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Slider;

class SliderTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Slider $slider)
    {
        return [
            'id' => $slider->id,
            'name' => $slider->name,
            'url' => $slider->url,
            'picture' => $slider->picture->url
        ];
    }
}
