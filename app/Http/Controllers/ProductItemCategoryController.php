<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductItemCategoryMetaRequest;
use App\Http\Requests\ProductItemCategoryRequest;
use App\Models\ProductItem;
use App\Models\ProductItemCategory;
use App\Models\ProductItemCategoryMeta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ProductItemCategoryController extends Controller
{
    private string $title;

    public function __construct()
    {
        $this->title = 'Product Item Category';

        $this->middleware(['permission:View Product Item Category'])->only('index', 'show');
        $this->middleware(['permission:Create Product Item Category'])->only(['create', 'store']);
        $this->middleware(['permission:Edit Product Item Category'])->only('edit', 'update');
        $this->middleware(['permission:Delete Product Item Category'])->only('destroy');
    }

    public function index()
    {
        $productItemCategories = ProductItemCategory::latest()
            ->when(\request('name'), fn(Builder $q) => $q->where('name', 'like', '%'. \request('name') .'%'))
            ->paginate();

        $createLink = route('product_item_category.create');

        $title = $this->title;

        return view('product_item_categories.index', compact('productItemCategories', 'createLink', 'title'));
    }

    public function create()
    {
        $actionLink = route('product_item_category.store');
        $indexLink = route('product_item_category.index');

        $title = $this->title;

        return view('product_item_categories.form', compact('actionLink', 'indexLink', 'title'));
    }

    public function show(ProductItemCategory $productItemCategory)
    {
        $editLink = route('product_item_category.edit', $productItemCategory);
        $deleteLink = route('product_item_category.destroy', $productItemCategory);
        $indexLink = route('product_item_category.index');

        $title = $this->title;

        return view('product_item_categories.show', compact('productItemCategory', 'editLink', 'indexLink', 'deleteLink', 'title'));
    }

    public function store(ProductItemCategoryRequest $request)
    {
        ProductItemCategory::create($request->all());

        toast(alert_created_text($this->title),'success');
        return redirect()->route('product_item_category.index');
    }

    public function edit(ProductItemCategory $productItemCategory)
    {
        $actionLink = route('product_item_category.update', $productItemCategory);
        $indexLink = route('product_item_category.index');

        $title = $this->title;

        return view('product_item_categories.form', compact('actionLink', 'indexLink', 'productItemCategory', 'title'));
    }

    public function update(ProductItemCategoryRequest $request, ProductItemCategory $productItemCategory)
    {
        $productItemCategory->update($request->all());

        toast(alert_updated_text($this->title),'success');
        return redirect()->route('product_item_category.index');
    }

    public function destroy(ProductItemCategory $productItemCategory)
    {
        $productItemCategory->metas()->delete();
        $productItemCategory->delete();

        toast(alert_deleted_text($this->title),'success');
        return redirect()->route('product_item_category.index');
    }

    public function metaCreate(ProductItemCategory $productItemCategory)
    {
        $title = "Item {$productItemCategory->product->name} $productItemCategory->name";
        $actionLink = route('product_item_categories.metas.store', ['product_item_category' => $productItemCategory]);
        $productItems = ProductItem::where('product_id', $productItemCategory->product_id)->where('status', 'active')->get();

        return view('product_item_categories.meta-form', compact('title', 'productItemCategory', 'productItems', 'actionLink'));
    }

    public function metaEdit(ProductItemCategory $productItemCategory, ProductItemCategoryMeta $meta)
    {
        $title = "Item {$productItemCategory->product->name} $productItemCategory->name";
        $actionLink = route('product_item_categories.metas.update', ['product_item_category' => $productItemCategory, 'meta' => $meta]);
        $productItems = ProductItem::where('product_id', $productItemCategory->product_id)->where('status', 'active')->get();

        return view('product_item_categories.meta-form', compact('title', 'productItemCategory', 'productItems', 'actionLink', 'meta'));
    }

    public function metaStore(ProductItemCategoryMetaRequest $request, ProductItemCategory $productItemCategory)
    {
        try {
            DB::beginTransaction();

            $meta = ProductItemCategoryMeta::create($request->all());

            ProductItem::whereIn('id', $request->product_item_ids)
                ->update(['product_item_category_meta_id' => $meta->id]);

            insert_picture(request('picture'), $meta);

            DB::commit();

            toast(alert_created_text($this->title), 'success');

            return redirect()->route('product_item_category.show', [
                'product_item_category' => $productItemCategory->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            toast($e->getMessage(), 'error');

            return redirect()->back();
        }
    }

    public function metaUpdate(ProductItemCategoryMetaRequest $request, ProductItemCategory $productItemCategory, ProductItemCategoryMeta $meta)
    {
        try {
            DB::beginTransaction();

            $meta->update($request->all());

            ProductItem::whereIn('id', $request->product_item_ids)
                ->update(['product_item_category_meta_id' => $meta->id]);

            if ($request->picture) {
                insert_picture(request('picture'), $meta);
            }

            DB::commit();

            toast(alert_updated_text($this->title), 'success');

            return redirect()->route('product_item_category.show', [
                'product_item_category' => $productItemCategory->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            toast($e->getMessage(), 'error');

            return redirect()->back();
        }
    }

    public function metaDestroy(ProductItemCategoryMeta $meta)
    {
        try {
            DB::beginTransaction();
            $productItemCategoryId = $meta->productItemCategory->id;
            $meta->delete();
            ProductItem::where('product_item_category_meta_id', $meta->id)
                ->update(['product_item_category_meta_id' => null]);
            DB::commit();

            toast(alert_deleted_text($this->title), 'success');

            return redirect()->route('product_item_category.show', [
                'product_item_category' => $productItemCategoryId
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            toast($e->getMessage(), 'error');

            return redirect()->back();
        }
    }
}
