<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    private $title;

    public function __construct()
    {
        $this->title = 'Produk';

        $this->middleware(['permission:View Product Category'])->only('index', 'show');
        $this->middleware(['permission:Create Product Category'])->only(['create', 'store']);
        $this->middleware(['permission:Edit Product Category'])->only('edit', 'update');
        $this->middleware(['permission:Delete Product Category'])->only('destroy');
    }

    public function index()
    {
        $products = Product::active()
            ->latest()
            ->when(request('name'), function ($query) {
                return $query->where('name', 'like', '%' . request('name') . '%');
            })
            ->paginate();

        $createLink = route('product.create');

        $title = $this->title;

        return view('products.index', compact('products', 'createLink', 'title'));
    }

    // public function create()
    // {
    //     $storeLink = route('product.store');
    //     $indexLink = route('product.index');
    //
    //     $title = $this->title;
    //
    //     return view('products.create', compact('storeLink', 'indexLink', 'title'));
    // }

    public function show(Product $product)
    {
        $editLink = route('product.edit', $product);
        $deleteLink = route('product.destroy', $product);
        $indexLink = route('product.index');

        $title = $this->title;

        return view('products.show', compact('product', 'editLink', 'indexLink', 'deleteLink', 'title'));
    }

    public function store(ProductRequest $request)
    {
        $product = Product::create($request->all());

        toast(alert_created_text($this->title), 'success');

        return redirect()->route('product.index');
    }

    public function edit(Product $product)
    {
        $updateLink = route('product.update', $product);
        $indexLink = route('product.index');

        $title = $this->title;

        return view('products.edit', compact('updateLink', 'indexLink', 'product', 'title'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->all());

        toast(alert_updated_text($this->title), 'success');
        return redirect()->route('product.index');
    }

    public function destroy(Product $product)
    {
        DB::table('discount_product')
            ->where('productable_id', $product->id)
            ->where('productable_type', 'App\Models\Product')
            ->delete();

        $product->productClient->first()?->picture()?->delete();

        $product->delete();

        toast(alert_deleted_text($this->title), 'success');

        return redirect()->route('product.index');
    }
}
