<?php

namespace App\Http\Controllers;

use App\Models\ProductItemCategory;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ProductItemCategoryController extends Controller
{
    private $title;

    public function __construct()
    {
        $this->title = 'Produk Item Kategori';

        $this->middleware(['permission:View Product Item Category'])->only('index', 'show');
        $this->middleware(['permission:Create Product Item Category'])->only(['create', 'store']);
        $this->middleware(['permission:Edit Product Item Category'])->only('edit', 'update');
        $this->middleware(['permission:Delete Product Item Category'])->only('destroy');
    }

    public function index()
    {
        /** @var \App\Models\Client $client */
        $client = Auth::user()->client;
        $productItemCategories = ProductItemCategory::query()
            ->active()
            ->paginate();

        $createLink = route('product_item_category.create');

        $title = $this->title;

        return view('product_item_categories.index', compact('productItemCategories', 'createLink', 'title'));
    }

    public function show(ProductItemCategory $productItemCategory)
    {
        $editLink = route('product_item_category.edit', $productItemCategory);
        $deleteLink = route('product_item_category.destroy', $productItemCategory);
        $indexLink = route('product_item_category.index');

        $title = $this->title;

        return view('product_item_categories.show', compact('productItemCategory', 'editLink', 'indexLink', 'deleteLink', 'title'));
    }
}
