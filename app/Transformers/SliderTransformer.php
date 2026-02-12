<?php

namespace App\Transformers;

use App\Models\Slider;
use League\Fractal\TransformerAbstract;

class SliderTransformer extends TransformerAbstract
{
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
