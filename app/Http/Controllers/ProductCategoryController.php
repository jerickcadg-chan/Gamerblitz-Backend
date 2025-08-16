<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCategoryRequest;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    private $title;

    public function __construct()
    {
        $this->title = 'Product Category';

        $this->middleware(['permission:View Product Category'])->only('index', 'show');
        $this->middleware(['permission:Create Product Category'])->only(['create', 'store']);
        $this->middleware(['permission:Edit Product Category'])->only('edit', 'update');
        $this->middleware(['permission:Delete Product Category'])->only('destroy');
    }

    public function index()
    {
        $productCategories = ProductCategory::latest()->paginate();

        $createLink = route('product_category.create');

        $title = $this->title;

        return view('product_categories.index', compact('productCategories', 'createLink', 'title'));
    }

    public function create()
    {
        $storeLink = route('product_category.store');
        $indexLink = route('product_category.index');

        $title = $this->title;

        return view('product_categories.create', compact('storeLink', 'indexLink', 'title'));
    }

    public function show(ProductCategory $productCategory)
    {
        $editLink = route('product_category.edit', $productCategory);
        $deleteLink = route('product_category.destroy', $productCategory);
        $indexLink = route('product_category.index');

        $title = $this->title;

        return view('product_categories.show', compact('productCategory', 'editLink', 'indexLink', 'deleteLink', 'title'));
    }

    public function store(ProductCategoryRequest $request)
    {
        $request['slug'] = Str::slug($request->name);
        ProductCategory::create($request->all());

        toast(alert_created_text($this->title),'success');
        return redirect()->route('product_category.index');
    }

    public function edit(ProductCategory $productCategory)
    {
        $updateLink = route('product_category.update', $productCategory);
        $indexLink = route('product_category.index');

        $title = $this->title;

        return view('product_categories.edit', compact('updateLink', 'indexLink', 'productCategory', 'title'));
    }

    public function update(ProductCategoryRequest $request, ProductCategory $productCategory)
    {
        $request['slug'] = Str::slug($request->name);
        $productCategory->update($request->all());

        toast(alert_updated_text($this->title),'success');
        return redirect()->route('product_category.index');
    }

    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();

        toast(alert_deleted_text($this->title),'success');
        return redirect()->route('product_category.index');
    }
}
