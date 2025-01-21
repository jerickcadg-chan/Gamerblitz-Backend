<?php

namespace App\Http\Controllers\Api;

use App\Constants\ProductConstant;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function __invoke()
    {
        return api_status_ok(
            array_values(collect(ProductConstant::all())
                ->filter(function ($item) {
                    return $item != ProductConstant::getTitle('account') && $item != ProductConstant::getTitle('joki');
                })
                ->map(function ($item, $key) {
                    return [
                        'key' => $key,
                        'label' => $item,
                    ];
                })->toArray())
        );
    }
}
