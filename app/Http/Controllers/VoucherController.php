<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoucherRequest;
use App\Models\Voucher;
use App\Models\ProductItem;
use App\Imports\VoucherImport;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\OrderService;

class VoucherController extends Controller
{
    private $title;
    private $product_item_id;

    public function __construct()
    {
        $this->title = 'Voucher';

        $this->product_item_id = request('product_item_id');

        $this->middleware(['permission:View Voucher'])->only('index', 'show');
        $this->middleware(['permission:Create Voucher'])->only(['create', 'store']);
        $this->middleware(['permission:Edit Voucher'])->only('edit', 'update');
        $this->middleware(['permission:Delete Voucher'])->only('destroy');
    }

    public function index()
    {
        $vouchers = Voucher::latest()
            ->where('product_item_id', request('product_item_id'))
            ->when(request('serial_number'), function ($query) {
                return $query->where('serial_number', request('serial_number'));
            })
            ->paginate();

        $createLink = route('voucher.create', ['product_item_id' => $this->product_item_id]);

        $title = $this->title;

        $productItem = request('product_item_id')
            ? ProductItem::find(request('product_item_id'))
            : null;

        return view('vouchers.index', compact('vouchers', 'createLink', 'title', 'productItem'));
    }

    public function create()
    {
        $storeLink = route('voucher.store');
        $indexLink = route('voucher.index', ['product_item_id' => $this->product_item_id]);

        $title = $this->title;

        return view('vouchers.create', compact('storeLink', 'indexLink', 'title'));
    }

    public function show(Voucher $voucher)
    {
        $editLink = route('voucher.edit', $voucher);
        $deleteLink = route('voucher.destroy', $voucher);
        $indexLink = route('voucher.index');

        $title = $this->title;

        return view('vouchers.show', compact('voucher', 'editLink', 'indexLink', 'deleteLink', 'title'));
    }

    public function store(VoucherRequest $request, OrderService $orderService)
    {
        $voucher = Voucher::create($request->all());

        $orderService->updateVoucherStock($voucher->productItem);

        toast(alert_created_text($this->title),'success');
        return redirect()->route('voucher.index', ['product_item_id' => $voucher->product_item_id]);
    }

    public function edit(Voucher $voucher)
    {
        $updateLink = route('voucher.update', $voucher);
        $indexLink = route('voucher.index');

        $title = $this->title;

        return view('vouchers.edit', compact('updateLink', 'indexLink', 'voucher', 'title'));
    }

    public function update(VoucherRequest $request, Voucher $voucher)
    {
        $voucher->update($request->all());

        toast(alert_updated_text($this->title),'success');
        return redirect()->route('voucher.index', ['product_item_id' => $voucher->product_item_id]);
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();

        toast(alert_deleted_text($this->title),'success');
        return redirect()->back();
    }

    public function import()
    {
        $importLink = route('voucher.import-excel', ['product_item_id' => $this->product_item_id]);
        $indexLink = route('voucher.index', ['product_item_id' => $this->product_item_id]);

        $title = $this->title;

        return view('vouchers.import', compact('importLink', 'indexLink', 'title'));
    }

    public function importExcel(Request $request, OrderService $orderService)
    {
        $request->validate([
        	'excel' => 'required|mimes:csv,xls,xlsx'
        ]);

        try {
            \Excel::import(new VoucherImport, $request->excel);

            $productItem = ProductItem::find(request('product_item_id'));
            $orderService->updateVoucherStock($productItem);

            toast(alert_imported_text($this->title),'success');
            return redirect()->route('voucher.index', ['product_item_id' => request('product_item_id')]);
        } catch (\Exception $e) {
            toast(alert_error($e->getMessage()));
            return redirect()->back();
        }

    }
}
