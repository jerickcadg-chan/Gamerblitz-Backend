<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductItemRequest;
use App\Models\ProductItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ProductItemController extends Controller
{
    private $title;

    public function __construct()
    {
        $this->title = 'Item Produk';

        $this->middleware(['permission:View Product Item'])->only('index', 'show');
        $this->middleware(['permission:Create Product Item'])->only(['create', 'store']);
        $this->middleware(['permission:Edit Product Item'])->only('edit', 'update');
        $this->middleware(['permission:Delete Product Item'])->only('destroy');
    }

    public function index()
    {
        /** @var \App\Models\Client $client */
        $client = Auth::user()->client;
        $productItems = ProductItem::query()
            ->active()
            ->with([
                'productItemClients' => function ($query) use ($client) {
                    $query->where('client_id', $client->id);
                },
            ])
            ->latest()->with('product')
            ->when(request('name'), function ($query) {
                return $query->where('code', 'like', '%'.request("name").'%');
            })
            ->when(request('product_id'), function ($query) {
                return $query->where('product_id', request('product_id'));
            })
            ->doesnthave('accounts')
            ->paginate();

        $createLink = route('product_item.create');

        $title = $this->title;

        $products = Product::all();

        return view('product_items.index', compact('products', 'productItems', 'createLink', 'title'));
    }

    public function create()
    {
        $storeLink = route('product_item.store');
        $indexLink = route('product_item.index');

        $title = $this->title;

        $products = Product::all();

        return view('product_items.create', compact('products', 'storeLink', 'indexLink', 'title'));
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

        toast(alert_created_text($this->title),'success');
        return redirect()->route('product_item.index');
    }

    public function edit(ProductItem $productItem)
    {
        $updateLink = route('product_item.update', $productItem);
        $indexLink = route('product_item.index');

        $title = $this->title;

        $products = Product::all();

        return view('product_items.edit', compact('products', 'updateLink', 'indexLink', 'productItem', 'title'));
    }

    public function update(ProductItemRequest $request, ProductItem $productItem)
    {
        $productItem->update($request->all());

        toast(alert_updated_text($this->title),'success');
        return redirect()->route('product_item.index');
    }

    public function destroy(ProductItem $productItem)
    {
        $productItem->delete();

        toast(alert_deleted_text($this->title),'success');
        return redirect()->route('product_item.index');
    }
}
