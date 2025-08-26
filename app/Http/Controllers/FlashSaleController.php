<?php

namespace App\Http\Controllers;

use App\Http\Requests\FlashSaleRequest;
use App\Models\FlashSale;
use App\Models\Product;
use App\Models\ProductItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FlashSaleController extends Controller
{
    private string $title;

    public function __construct()
    {
        $this->title = 'Flash Sale';

        $this->middleware(['permission:View Flash Sales'])->only('index', 'show');
        $this->middleware(['permission:Create Flash Sales'])->only(['create', 'store']);
        $this->middleware(['permission:Edit Flash Sales'])->only('edit', 'update');
        $this->middleware(['permission:Delete Flash Sales'])->only('destroy');
    }

    public function index()
    {
        $flash_sales = FlashSale::latest()
            ->when(request('name'), function ($query) {
                return $query->where('name', 'like', '%' . request('name') . '%');
            })
            ->paginate();

        $createLink = route('flash_sale.create');

        $title = $this->title;

        return view('flash_sales.index', compact('flash_sales', 'createLink', 'title'));
    }

    public function create()
    {
        $title = $this->title;
        $storeLink = route('flash_sale.store');
        $indexLink = route('flash_sale.index');

        $products = Product::orderBy('name')->get(['id','name']);
        $productItems = ProductItem::with('product:id,name')
            ->where('product_id', request('product_id'))
            ->whereDoesntHave('flashSales')
            ->orderBy('name')
            ->get();

        return view('flash_sales.create', compact('title','storeLink','indexLink','products','productItems'));
    }

    public function store(FlashSaleRequest $request)
    {
        DB::transaction(function() use ($request) {
            foreach (($request->items ?? []) as $productItemId => $payload) {
                if (!isset($payload['selected']) || $payload['selected'] != 1) continue;
                $price = (int) ($payload['price'] ?? 0);
                $stock = (int) ($payload['stock'] ?? 0);
                if ($price <= 0) continue;

                FlashSale::create([
                    'product_item_id' => (int) $productItemId,
                    'price'           => $price,
                    'stock'           => $stock,
                ]);
            }
        });

        toast(alert_created_text($this->title), 'success');

        return redirect()->route('flash_sale.index');
    }

    public function edit(FlashSale $flash_sale)
    {
        $updateLink = route('flash_sale.update', $flash_sale);
        $indexLink = route('flash_sale.index');
        $title = $this->title;

        return view('flash_sales.edit', compact('updateLink', 'indexLink', 'flash_sale', 'title'));
    }

    public function update(Request $request, FlashSale $flash_sale)
    {
        $request->validate([
            'price' => ['required', 'numeric'],
            'stock' => ['required', 'numeric'],
        ]);

        $flash_sale->update($request->all());

        toast(alert_updated_text($this->title), 'success');

        return redirect()->route('flash_sale.index');
    }

    public function show(FlashSale $flash_sale)
    {
        $title = $this->title;

        return view('flash_sales.show', compact('flash_sale', 'title'));
    }

    public function destroy(FlashSale $flash_sale)
    {
        $flash_sale->delete();
        toast(alert_deleted_text($this->title), 'success');

        return redirect()->route('flash_sale.index');
    }
}
