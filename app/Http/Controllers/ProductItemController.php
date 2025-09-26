<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductItemRequest;
use App\Jobs\FetchVarianHandle;
use App\Models\FetchVarianJob;
use App\Models\ProductItem;
use App\Models\Product;
use Illuminate\Routing\Controller;

class ProductItemController extends Controller
{
    private string $title;

    public function __construct()
    {
        $this->title = 'Product Item';

        $this->middleware(['permission:View Product Item'])->only('index', 'show');
        $this->middleware(['permission:Create Product Item'])->only(['create', 'store']);
        $this->middleware(['permission:Edit Product Item'])->only('edit', 'update');
        $this->middleware(['permission:Delete Product Item'])->only('destroy');
    }

    public function index()
    {
        $productItems = ProductItem::query()
            ->active()
            ->latest()->with('product')
            ->when(request('name'), function ($query) {
                return $query->where('name', 'like', '%' . request("name") . '%');
            })
            ->when(request('code'), function ($query) {
                return $query->where('code', 'like', '%' . request("code") . '%');
            })
            ->when(request('product_id'), function ($query) {
                return $query->where('product_id', request('product_id'));
            })
            ->paginate();

        $createLink = route('product_item.create');

        $title = $this->title;

        $products = Product::all();
        $jobVariant = FetchVarianJob::where('command_name', 'fetch:variant')
            ->where('status', 'PENDING')
            ->latest('created_at')
            ->first();

        return view('product_items.index', compact('products', 'productItems', 'createLink', 'title', 'jobVariant'));
    }

    public function create()
    {
        $actionLink = route('product_item.store');
        $indexLink = route('product_item.index');

        $title = $this->title;

        $products = Product::all();

        return view('product_items.form', compact('products', 'actionLink', 'indexLink', 'title'));
    }

    public function show(ProductItem $productItem)
    {
        $editLink = route('product_item.edit', $productItem);
        $deleteLink = route('product_item.destroy', $productItem);
        $indexLink = route('product_item.index');

        $title = $this->title;

        return view('product_items.show', compact('productItem', 'editLink', 'indexLink', 'deleteLink', 'title'));
    }

    public function store(ProductItemRequest $request)
    {
        ProductItem::create($request->all());

        toast(alert_created_text($this->title), 'success');
        return redirect()->route('product_item.index');
    }

    public function edit(ProductItem $productItem)
    {
        $actionLink = route('product_item.update', $productItem);
        $indexLink = route('product_item.index');

        $title = $this->title;

        $products = Product::all();

        return view('product_items.form', compact('products', 'actionLink', 'indexLink', 'productItem', 'title'));
    }

    public function update(ProductItemRequest $request, ProductItem $productItem)
    {
        $productItem->update($request->all());

        toast(alert_updated_text($this->title), 'success');
        return redirect()->route('product_item.index');
    }

    public function destroy(ProductItem $productItem)
    {
        $productItem->delete();

        toast(alert_deleted_text($this->title), 'success');
        return redirect()->route('product_item.index');
    }

    public function syncItem()
    {
        $status = FetchVarianJob::create([
            'command_name' => 'fetch:variant',
            'status' => 'PENDING',
        ]);

        FetchVarianHandle::dispatch($status->id);

        toast('Item is still syncing', 'success');

        return redirect()->back();
    }
}
