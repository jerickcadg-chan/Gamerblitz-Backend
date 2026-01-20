<?php

namespace App\Http\Controllers;

use App\Models\EcommerceCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class EcommerceCategoryController extends Controller
{
    private string $title;

    public function __construct()
    {
        $this->title = 'eCommerce Category';

        $this->middleware(['permission:View Ecommerce Category'])->only('index', 'show');
        $this->middleware(['permission:Create Ecommerce Category'])->only(['create', 'store']);
        $this->middleware(['permission:Edit Ecommerce Category'])->only('edit', 'update');
        $this->middleware(['permission:Delete Ecommerce Category'])->only('destroy');
    }

    public function index()
    {
        $categories = EcommerceCategory::latest()
            ->withCount('products')
            ->when(request('name'), function ($query) {
                return $query->where('name', 'like', '%' . request('name') . '%');
            })
            ->paginate();

        $createLink = route('ecommerce_category.create');
        $title = $this->title;

        return view('ecommerce.categories.index', compact('categories', 'createLink', 'title'));
    }

    public function create()
    {
        $formAction = route('ecommerce_category.store');
        $indexLink = route('ecommerce_category.index');
        $title = $this->title;

        return view('ecommerce.categories.form', compact('formAction', 'indexLink', 'title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        EcommerceCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('ecommerce_category.index')->with('success', 'Category created successfully.');
    }

    public function edit(EcommerceCategory $ecommerce_category)
    {
        $category = $ecommerce_category;
        $formAction = route('ecommerce_category.update', $category->id);
        $indexLink = route('ecommerce_category.index');
        $title = $this->title;

        return view('ecommerce.categories.form', compact('category', 'formAction', 'indexLink', 'title'));
    }

    public function update(Request $request, EcommerceCategory $ecommerce_category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $ecommerce_category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('ecommerce_category.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(EcommerceCategory $ecommerce_category)
    {
        if ($ecommerce_category->products()->count() > 0) {
            return redirect()->route('ecommerce_category.index')->with('error', 'Cannot delete category with products.');
        }

        $ecommerce_category->delete();

        return redirect()->route('ecommerce_category.index')->with('success', 'Category deleted successfully.');
    }
}

