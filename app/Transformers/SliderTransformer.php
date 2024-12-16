<?php

namespace App\Transformers;

use App\Models\Slider;
use League\Fractal\TransformerAbstract;

class SliderTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     */
    protected array $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     */
    protected array $availableIncludes = [
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
            'picture' => $slider->picture->url,
        ];
    }
}
