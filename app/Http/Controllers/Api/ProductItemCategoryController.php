<?php

namespace App\Http\Controllers\Api;

use App\Constants\ProductConstant;
use App\Http\Controllers\Controller;
use App\Models\ProductItemCategory;
use Illuminate\Http\Request;

class ProductItemCategoryController extends Controller
{
    public function index()
    {
        return api_status_ok(
            array_values(ProductItemCategory::active()
                ->get()
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
                    ];
                })
                ->toArray())
        );
    }

    public function indexWithMeta(Request $request)
    {
        $productId = $request->query('product_id');
        $q = ProductItemCategory::active()->with("metas.picture");
        if ($productId) {
            $q->where('product_id', $productId);
        }
        $data = $q->get();
        return api_status_ok(
            array_values(
                $data
                    ->filter(function (ProductItemCategory $item) {
                        return $item->name !=
                            ProductConstant::getTitle("account") &&
                            $item->name != ProductConstant::getTitle("joki");
                    })
                    ->map(function ($item) {
                        return array_merge(
                            $item->toArray(),
                            [
                                'metas' => $item->metas->map(function ($meta) {
                                    return array_merge(
                                        $meta->toArray(),
                                        ['picture' => $meta->picture->url],
                                    );
                                })
                            ]
                        );
                    })
                    ->toArray()
            )
        );
    }
}
