<?php

namespace App\Providers;

use App\Models\FlashSaleProductItem;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        Blade::directive('loadProductFlashSaleItem', function ($productFlashSaleItems) {
            $div = '';
            \App\Models\ProductItem::with('product')->chunk(200, function($product_item) use (&$div) {
                foreach ($product_item as $item) {
                    $div .= '<div class="item">';
                    $div .= '<input type="checkbox" name="product_item_ids[]" value="'.$item->id.'" class="my-2" '.(is_array(old('product_item_id')) && in_array($item->id, old('product_item_ids')) ? ' checked' : '').'>';
                    $div .= '<span> '.$item?->product?->name.' - '.$item?->name.'</span>';
                    $div .= '<br>';
                    $div .= '</div>';
                }
            });

            return $div;
        });
    }
}
