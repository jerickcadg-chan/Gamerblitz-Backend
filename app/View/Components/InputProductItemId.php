<?php

namespace App\View\Components;

use App\Models\FlashSaleProductItem;
use App\Models\ProductItem;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InputProductItemId extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ProductItem $productItem,
        public int $index,
        public $flashSaleProductItems = null
    ) {
    }

    public function isChecked(): bool
    {
        if (old('product_item_ids') != null) {
            return old("product_item_ids.{$this->index}.product_item_id") == $this->productItem->id;
        }

        if ($this->flashSaleProductItems) {
            return $this->flashSaleProductItems->contains('product_item_id', $this->productItem->id);
        }

        return false;
    }

    public function getFlashSaleProductItem(): FlashSaleProductItem
    {
        if (old('product_item_ids') != null) {
            return new FlashSaleProductItem([
                'product_item_id' => old("product_item_ids.{$this->index}.product_item_id"),
                'stock' => old("product_item_ids.{$this->index}.stock"),
                'price' => old("product_item_ids.{$this->index}.price"),
            ]);

        }
        if ($this->flashSaleProductItems) {
            $flashSaleProductItem = $this->flashSaleProductItems->firstWhere('product_item_id', $this->productItem->id);
            if ($flashSaleProductItem) {
                return $flashSaleProductItem;
            }
        }

        return new FlashSaleProductItem();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.input-product-item-id');
    }
}
