<?php

namespace App\Http\Controllers\Api;

use App\Constants\ProductConstant;
use App\Http\Controllers\Controller;
use App\Models\ProductCategory;

class CategoryController extends Controller
{
    public function __invoke()
    {
        return api_status_ok(
            array_values(ProductCategory::all()
                ->filter(function (ProductCategory $item) {
                    return $item->name != ProductConstant::getTitle('account') && $item->name != ProductConstant::getTitle('joki');
                })
                ->map(function ($item) {
                    return [
                        'key' => $item->slug,
                        'label' => $item->name,
                    ];
                })->toArray())
        );
    }
}
