<?php

namespace App\Http\Controllers\Api;

use App\Constants\ProductConstant;
use App\Http\Controllers\Controller;
use App\Models\ProductItemCategory;

class ProductItemCategoryController extends Controller
{
    public function __invoke()
    {
        $data = ProductItemCategory::active()->with("metas.picture")->get();
        return api_status_ok(
            array_values(
                $data
                    ->filter(function (ProductItemCategory $item) {
                        return $item->name !=
                            ProductConstant::getTitle("account") &&
                            $item->name != ProductConstant::getTitle("joki");
                    })
                    ->map(function ($item) {
                        return [
                            "id" => $item->id,
                            "key" => $item->slug,
                            "label" => $item->name,
                            "metas" => $item->metas,
                        ];
                    })
                    ->toArray()
            )
        );
    }
}
