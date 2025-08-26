<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Services\PictureService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    private string $title;

    public function __construct()
    {
        $this->title = 'Product';

        $this->middleware(['permission:View Product'])->only('index', 'show');
        $this->middleware(['permission:Create Product'])->only(['create', 'store']);
        $this->middleware(['permission:Edit Product'])->only('edit', 'update');
        $this->middleware(['permission:Delete Product'])->only('destroy');
    }

    public function index()
    {
        $products = Product::latest()
            ->when(request('name'), function ($query) {
                return $query->where('name', 'like', '%' . request('name') . '%');
            })
            ->paginate();

        $createLink = route('product.create');

        $title = $this->title;

        return view('products.index', compact('products', 'createLink', 'title'));
    }

     public function create()
     {
         $formAction = route('product.store');
         $indexLink = route('product.index');

         $title = $this->title;

         return view('products.form', compact('formAction', 'indexLink', 'title'));
     }

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
        $pictureService = new PictureService();

        if ($request->hasFile('cover'))   $request['default_cover']   = $pictureService->insert($request->cover);
        if ($request->hasFile('picture')) $request['default_picture'] = $pictureService->insert($request->picture);

        Product::create($request->all());

        toast(alert_created_text($this->title), 'success');

        return redirect()->route('product.index');
    }

    public function edit(Product $product)
    {
        $formAction = route('product.update', $product);
        $indexLink = route('product.index');

        $title = $this->title;

        return view('products.form', compact('formAction', 'indexLink', 'product', 'title'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $pictureService = new PictureService();

        if ($request->hasFile('cover'))   $request['default_cover']   = $pictureService->insert($request->cover);
        if ($request->hasFile('picture')) $request['default_picture'] = $pictureService->insert($request->picture);

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

        $product->delete();

        toast(alert_deleted_text($this->title), 'success');

        return redirect()->route('product.index');
    }
}
