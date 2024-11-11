<?php

namespace App\Http\Controllers\Api;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Transformers\SliderTransformer;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::all();

        return api_status_ok(transformer($sliders, new SliderTransformer));
    }
}
