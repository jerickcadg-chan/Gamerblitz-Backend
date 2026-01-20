<?php

namespace App\Http\Controllers\Api;

use App\Models\Slider;
use App\Transformers\SliderTransformer;
use Illuminate\Routing\Controller;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::get();

        return api_status_ok(transformer($sliders, new SliderTransformer));
    }
}
