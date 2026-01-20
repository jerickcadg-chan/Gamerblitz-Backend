<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiscountRequest;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class DiscountController extends Controller
{
    private string $title;

    public function __construct()
    {
        $this->title = 'Discount';

        $this->middleware(['permission:View Discount'])->only('index', 'show');
        $this->middleware(['permission:Create Discount'])->only(['create', 'store']);
        $this->middleware(['permission:Edit Discount'])->only('edit', 'update');
        $this->middleware(['permission:Delete Discount'])->only('destroy');
    }

    public function index()
    {
        $discounts = Discount::latest()
            ->when(request('name'), function ($query) {
                return $query->where('name', 'like', '%'. request('name') .'%');
            })
            ->paginate();

        $createLink = route('discount.create');

        $title = $this->title;

        return view('discounts.index', compact('discounts', 'createLink', 'title'));
    }

    public function create()
    {
        $formAction = route('discount.store');
        $indexLink = route('discount.index');

        $title = $this->title;

        return view('discounts.form', compact('formAction', 'indexLink', 'title'));
    }

    public function show(Discount $discount)
    {
        $editLink = route('discount.edit', $discount);
        $deleteLink = route('discount.destroy', $discount);
        $indexLink = route('discount.index');

        $title = $this->title;

        return view('discounts.show', compact('discount', 'editLink', 'indexLink', 'deleteLink', 'title'));
    }

    public function store(DiscountRequest $request)
    {
        $discount = Discount::create($request->all());

        $this->extracted($request, $discount);

        toast(alert_created_text($this->title),'success');
        return redirect()->route('discount.index');
    }

    public function edit(Discount $discount)
    {
        $formAction = route('discount.update', $discount);
        $indexLink = route('discount.index');

        $title = $this->title;

        return view('discounts.form', compact('formAction', 'indexLink', 'discount', 'title'));
    }

    public function update(DiscountRequest $request, Discount $discount)
    {
        $discount->update($request->all());

        DB::table('discount_product')->where('discount_id', $discount->id)->delete();

        $this->extracted($request, $discount);

        toast(alert_updated_text($this->title),'success');
        return redirect()->route('discount.index');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();

        toast(alert_deleted_text($this->title),'success');
        return redirect()->route('discount.index');
    }

    /**
     * @param DiscountRequest $request
     * @param Discount $discount
     * @return void
     */
    public function extracted(DiscountRequest $request, Discount $discount): void
    {
        switch ($request->product_type) {
            case Discount::PRODUCT_TYPE:
                foreach ($request->product_id as $product) {
                    DB::table('discount_product')->insert([
                        'discount_id' => $discount->id,
                        'productable_id' => $product,
                        'productable_type' => \App\Models\Product::class,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                break;

            case Discount::PRODUCT_ITEM:
                foreach ($request->product_item_id as $product) {
                    DB::table('discount_product')->insert([
                        'discount_id' => $discount->id,
                        'productable_id' => $product,
                        'productable_type' => \App\Models\ProductItem::class,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                break;
        }
    }
}
