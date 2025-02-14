<?php

namespace App\Http\Controllers;

use App\Models\FlashSale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FlashSaleController extends Controller
{
    private $title;

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
            ->whereClient()
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
        $storeLink = route('flash_sale.store');
        $indexLink = route('flash_sale.index');

        $title = $this->title;

        return view('flash_sales.create', compact('storeLink', 'indexLink', 'title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable',
            'start_date' => ['required', 'date', 'before:end_date', function ($attribute, $value, $fail) use ($request) {
                $exist = FlashSale::query()
                    ->where('client_id', client()->id)
                    ->where(function (Builder $query) use ($request) {
                        $query->where('start_date', '<=', now()->parse($request->end_date)->format('Y-m-d H:i:s'))
                            ->where('end_date', '>=', now()->parse($request->start_date)->format('Y-m-d H:i:s'));
                    })
                    ->exists();

                if ($exist) {
                    $fail('Flash sale in this date range already exist');
                }
            }],
            'end_date' => ['required', 'date', 'after:start_date'],
            'product_item_ids' => ['required', 'array'],
            'product_item_ids.*.product_item_id' => ['required', 'exists:product_items,id'],
            'product_item_ids.*.price' => ['required', 'numeric', 'min:0'],
            'product_item_ids.*.stock' => ['required', 'integer', 'min:0']
        ]);

        /** @var \App\Models\FlashSale $flash_sale */
        $flash_sale = FlashSale::create(array_merge($request->all(), [
            'client_id' => auth()->user()->client->id,
        ]));

        $flash_sale->items()->createMany(array_map(function ($productItem) {
            return [
                'product_item_id' => $productItem["product_item_id"],
                'price' => $productItem["price"],
                'stock' => $productItem['stock'],
            ];
        }, $request->product_item_ids));

        toast(alert_created_text($this->title), 'success');

        return redirect()->route('flash_sale.show', $flash_sale);
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
            'name' => 'nullable',
            'start_date' => ['required', 'date', 'before:end_date', function ($attribute, $value, $fail) use ($request, $flash_sale) {
                $exist = FlashSale::query()
                    ->where('client_id', client()->id)
                    ->where('id', '!=', $flash_sale->id)
                    ->where(function (Builder $query) use ($request) {
                        $query->where('start_date', '<=', now()->parse($request->end_date)->format('Y-m-d H:i:s'))
                            ->where('end_date', '>=', now()->parse($request->start_date)->format('Y-m-d H:i:s'));
                    })
                    ->exists();

                if ($exist) {
                    $fail('Flash sale in this date range already exist');
                }
            }],
            'end_date' => ['required', 'date', 'after:start_date'],
            'product_item_ids' => ['required', 'array'],
            'product_item_ids.*.product_item_id' => ['required', 'exists:product_items,id'],
            'product_item_ids.*.price' => ['required', 'numeric', 'min:0'],
            'product_item_ids.*.stock' => ['required', 'integer', 'min:0']
        ]);

        $flash_sale->update($request->all());
        $flash_sale->items()->delete();
        $flash_sale->items()->createMany(array_map(function ($productItem) {
            return [
                'product_item_id' => $productItem["product_item_id"],
                'price' => $productItem["price"],
                'stock' => $productItem['stock'],
            ];
        }, $request->product_item_ids));

        toast(alert_updated_text($this->title), 'success');

        return redirect()->route('flash_sale.show', $flash_sale);
    }

    public function show(FlashSale $flash_sale)
    {
        $flash_sale->load('items.productItem');
        $editLink = route('flash_sale.edit', $flash_sale);
        $deleteLink = route('flash_sale.destroy', $flash_sale);
        $indexLink = route('flash_sale.index');
        $title = $this->title;

        return view('flash_sales.show', compact('flash_sale', 'editLink', 'indexLink', 'deleteLink', 'title'));
    }

    public function destroy(FlashSale $flash_sale)
    {
        $flash_sale->delete();
        toast(alert_deleted_text($this->title), 'success');

        return redirect()->route('flash_sale.index');
    }
}
