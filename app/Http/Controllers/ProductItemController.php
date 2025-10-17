<?php

namespace App\Http\Controllers;

use App\Constants\ProviderConstant;
use App\Http\Requests\ProductItemRequest;
use App\Jobs\FetchVarianHandle;
use App\Models\FetchVarianJob;
use App\Models\ProductItem;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

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
        $isSyncRunning = Cache::has('vexagame-sync') || Cache::has(key: 'lapakgaming-sync');

        return view('product_items.index', compact('products', 'productItems', 'createLink', 'title', 'isSyncRunning'));
    }

    public function create()
    {
        $actionLink = route('product_item.store');
        $indexLink = route('product_item.index');

        $title = $this->title;

        $products = Product::all();
        $providers = ProviderConstant::AVAILABLE_PROVIDER;

        return view('product_items.form', compact('products', 'providers', 'actionLink', 'indexLink', 'title'));
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
        $fallbackMarginPublic = Setting::getByKey('margin_public');
        $fallbackMarginSilver = Setting::getByKey('margin_silver');
        $fallbackMarginGold = Setting::getByKey('margin_gold');
        $fallbackMarginVip = Setting::getByKey('margin_vip');

        $newProductItem = new ProductItem();
        $newProductItem->fill($request->all());
        $newProductItem->margin = $newProductItem->margin ?? $fallbackMarginPublic;
        $newProductItem->margin_silver = $newProductItem->margin_silver ?? $fallbackMarginSilver;
        $newProductItem->margin_gold = $newProductItem->margin_gold ?? $fallbackMarginGold;
        $newProductItem->margin_vip = $newProductItem->margin_vip ?? $fallbackMarginVip;
        $newProductItem->provider = $newProductItem->provider ?? $newProductItem->product->provider;

        $newProductItem->save();

        toast(alert_created_text($this->title), 'success');
        return redirect()->route('product_item.index');
    }

    public function edit(ProductItem $productItem)
    {
        $actionLink = route('product_item.update', $productItem);
        $indexLink = route('product_item.index');

        $title = $this->title;

        $products = Product::all();
        $providers = ProviderConstant::AVAILABLE_PROVIDER;

        return view('product_items.form', compact('products', 'providers', 'actionLink', 'indexLink', 'productItem', 'title'));
    }

    public function update(ProductItemRequest $request, ProductItem $productItem)
    {
        $fallbackMarginPublic = Setting::getByKey('margin_public');
        $fallbackMarginSilver = Setting::getByKey('margin_silver');
        $fallbackMarginGold = Setting::getByKey('margin_gold');
        $fallbackMarginVip = Setting::getByKey('margin_vip');

        $productItem->fill($request->all());
        $productItem->margin = $productItem->margin ?? $fallbackMarginPublic;
        $productItem->margin_silver = $productItem->margin_silver ?? $fallbackMarginSilver;
        $productItem->margin_gold = $productItem->margin_gold ?? $fallbackMarginGold;
        $productItem->margin_vip = $productItem->margin_vip ?? $fallbackMarginVip;
        $productItem->provider = $productItem->provider ?? $productItem->product->provider;
        $productItem->save();

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
            'command_name' => 'app:sync-lapak-gaming',
            'status' => 'PENDING',
        ]);

        FetchVarianHandle::dispatch($status->id);

        toast('Item is still syncing', 'success');

        return redirect()->back();
    }
}
