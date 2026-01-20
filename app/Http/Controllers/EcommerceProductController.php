<?php

namespace App\Http\Controllers;

use App\Models\EcommerceCategory;
use App\Models\EcommerceProduct;
use App\Models\EcommerceVariantOption;
use App\Models\EcommerceProductVariant;
use App\Models\EcommerceProductLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EcommerceProductController extends Controller
{
    protected $title = 'Products';

    public function __construct()
    {
        $this->middleware('permission:View Ecommerce Product')->only(['index', 'show']);
        $this->middleware('permission:Create Ecommerce Product')->only(['create', 'store']);
        $this->middleware('permission:Edit Ecommerce Product')->only(['edit', 'update']);
        $this->middleware('permission:Delete Ecommerce Product')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = EcommerceProduct::with(['category', 'variantOptions', 'variants'])->latest();

        // Filter by name
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(10)->withQueryString();
        $categories = EcommerceCategory::where('is_active', true)->orderBy('name')->get();

        return view('ecommerce.products.index', [
            'title' => $this->title,
            'products' => $products,
            'categories' => $categories,
            'createLink' => route('ecommerce_product.store'),
        ]);
    }

    public function create()
    {
        $categories = EcommerceCategory::where('is_active', true)->orderBy('name')->get();

        return view('ecommerce.products.form', [
            'title' => $this->title,
            'indexLink' => route('ecommerce_product.index'),
            'formAction' => route('ecommerce_product.store'),
            'categories' => $categories,
            'variantData' => [],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:ecommerce_categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'capital_price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'meta_keywords' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['track_stock'] = $request->has('track_stock');
        $validated['capital_price'] = $request->input('capital_price', 0) ?: 0;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('ecommerce/products', 'public');
        }

        $product = EcommerceProduct::create($validated);

        EcommerceProductLog::logChange(
            $product->id,
            'created',
            null,
            null,
            null,
            "Product '{$product->name}' was created"
        );

        $this->saveVariants($request, $product, true);

        return redirect()->route('ecommerce_product.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(EcommerceProduct $ecommerce_product)
    {
        $product = $ecommerce_product;
        $categories = EcommerceCategory::where('is_active', true)->orderBy('name')->get();

        $variantData = $product->variantOptions->map(function ($option) {
            return [
                'id' => $option->id,
                'name' => $option->name,
                'values' => $option->values->map(function ($value) {
                    return [
                        'id' => $value->id,
                        'name' => $value->name,
                        'price' => $value->price,
                        'sale_price' => $value->sale_price,
                        'capital_price' => $value->capital_price,
                        'stock' => $value->stock,
                        'is_active' => $value->is_active,
                    ];
                })->toArray(),
            ];
        })->toArray();

        return view('ecommerce.products.form', [
            'title' => $this->title,
            'indexLink' => route('ecommerce_product.index'),
            'formAction' => route('ecommerce_product.update', $product),
            'categories' => $categories,
            'product' => $product,
            'variantData' => $variantData,
        ]);
    }

    public function update(Request $request, EcommerceProduct $ecommerce_product)
    {
        $product = $ecommerce_product;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:ecommerce_categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'capital_price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'meta_keywords' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['track_stock'] = $request->has('track_stock');
        $validated['capital_price'] = $request->input('capital_price', 0) ?: 0;

        $this->logProductChanges($product, $validated, $request);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
                EcommerceProductLog::logChange(
                    $product->id,
                    'updated',
                    'image',
                    $product->image,
                    'new_image',
                    "Product image was changed"
                );
            }
            $validated['image'] = $request->file('image')->store('ecommerce/products', 'public');
        }

        $product->update($validated);

        $this->saveVariants($request, $product, false);

        return redirect()->route('ecommerce_product.index')
            ->with('success', 'Product updated successfully.');
    }

    protected function logProductChanges(EcommerceProduct $product, array $validated, Request $request)
    {
        $fieldsToTrack = [
            'name' => 'Name',
            'description' => 'Description',
            'price' => 'Base Price',
            'sale_price' => 'Sale Price',
            'capital_price' => 'Capital Price',
            'stock' => 'Stock',
            'is_active' => 'Active Status',
            'is_featured' => 'Featured Status',
            'track_stock' => 'Track Stock',
            'meta_title' => 'SEO Title',
            'meta_description' => 'SEO Description',
            'meta_keywords' => 'SEO Keywords',
            'category_id' => 'Category',
        ];

        foreach ($fieldsToTrack as $field => $label) {
            $oldValue = $product->{$field};
            $newValue = $validated[$field] ?? null;

            if (in_array($field, ['is_active', 'is_featured', 'track_stock'])) {
                $oldValue = (bool) $oldValue;
                $newValue = (bool) $newValue;
            }

            if (in_array($field, ['price', 'sale_price', 'capital_price'])) {
                $oldValue = (float) $oldValue;
                $newValue = (float) ($newValue ?? 0);
            }

            if ($oldValue != $newValue) {
                $oldDisplay = $oldValue;
                $newDisplay = $newValue;

                if (in_array($field, ['is_active', 'is_featured', 'track_stock'])) {
                    $oldDisplay = $oldValue ? 'Yes' : 'No';
                    $newDisplay = $newValue ? 'Yes' : 'No';
                }

                if ($field === 'category_id') {
                    $oldCat = EcommerceCategory::find($oldValue);
                    $newCat = EcommerceCategory::find($newValue);
                    $oldDisplay = $oldCat ? $oldCat->name : 'None';
                    $newDisplay = $newCat ? $newCat->name : 'None';
                }

                EcommerceProductLog::logChange(
                    $product->id,
                    'updated',
                    $field,
                    $oldDisplay,
                    $newDisplay,
                    "{$label} changed from '{$oldDisplay}' to '{$newDisplay}'"
                );
            }
        }
    }

    protected function saveVariants(Request $request, EcommerceProduct $product, bool $isNew = false)
    {
        $variantOptions = $request->input('variant_options', []);
        $variantImages = $request->file('variant_images', []);

        $existingOptionIds = $product->variantOptions->pluck('id')->toArray();
        $submittedOptionIds = [];

        foreach ($variantOptions as $optionIndex => $optionData) {
            if (empty($optionData['name'])) {
                continue;
            }

            $optionId = $optionData['id'] ?? null;

            if ($optionId && in_array($optionId, $existingOptionIds)) {
                $option = EcommerceVariantOption::find($optionId);
                if ($option && $option->name !== $optionData['name']) {
                    EcommerceProductLog::logChange(
                        $product->id,
                        'variant_option_updated',
                        'option_name',
                        $option->name,
                        $optionData['name'],
                        "Variant option name changed from '{$option->name}' to '{$optionData['name']}'"
                    );
                }
                $option->update(['name' => $optionData['name']]);
                $submittedOptionIds[] = $optionId;
            } else {
                $option = $product->variantOptions()->create([
                    'name' => $optionData['name'],
                ]);
                $submittedOptionIds[] = $option->id;

                if (!$isNew) {
                    EcommerceProductLog::logChange(
                        $product->id,
                        'variant_option_added',
                        null,
                        null,
                        $optionData['name'],
                        "Variant option '{$optionData['name']}' was added"
                    );
                }
            }

            $values = $optionData['values'] ?? [];
            $existingValueIds = $option->values->pluck('id')->toArray();
            $submittedValueIds = [];

            foreach ($values as $valueIndex => $valueData) {
                if (empty($valueData['name'])) {
                    continue;
                }

                $valueId = $valueData['id'] ?? null;
                $imageFile = $variantImages[$optionIndex][$valueIndex] ?? null;

                $variantData = [
                    'name' => $valueData['name'],
                    'price' => $valueData['price'] ?? $product->price,
                    'sale_price' => $valueData['sale_price'] ?? null,
                    'capital_price' => $valueData['capital_price'] ?? 0,
                    'stock' => $valueData['stock'] ?? 0,
                    'is_active' => isset($valueData['is_active']),
                ];

                if ($imageFile) {
                    $variantData['image'] = $imageFile->store('ecommerce/variants', 'public');
                }

                if ($valueId && in_array($valueId, $existingValueIds)) {
                    $variant = EcommerceProductVariant::find($valueId);
                    if ($variant) {
                        $this->logVariantChanges($product, $variant, $variantData, $optionData['name']);
                        $variant->update($variantData);
                    }
                    $submittedValueIds[] = $valueId;
                } else {
                    $newVariant = $option->values()->create($variantData);
                    $submittedValueIds[] = $newVariant->id;

                    if (!$isNew) {
                        EcommerceProductLog::logChange(
                            $product->id,
                            'variant_added',
                            null,
                            null,
                            json_encode($variantData),
                            "Variant '{$optionData['name']}: {$valueData['name']}' was added with price " . number_format($variantData['price'], 2)
                        );
                    }
                }
            }

            $valuesToDelete = array_diff($existingValueIds, $submittedValueIds);
            foreach ($valuesToDelete as $deleteId) {
                $deletedVariant = EcommerceProductVariant::find($deleteId);
                if ($deletedVariant) {
                    EcommerceProductLog::logChange(
                        $product->id,
                        'variant_deleted',
                        null,
                        $deletedVariant->name,
                        null,
                        "Variant '{$optionData['name']}: {$deletedVariant->name}' was deleted"
                    );
                    if ($deletedVariant->image) {
                        Storage::disk('public')->delete($deletedVariant->image);
                    }
                    $deletedVariant->delete();
                }
            }
        }

        $optionsToDelete = array_diff($existingOptionIds, $submittedOptionIds);
        foreach ($optionsToDelete as $deleteId) {
            $deletedOption = EcommerceVariantOption::with('values')->find($deleteId);
            if ($deletedOption) {
                EcommerceProductLog::logChange(
                    $product->id,
                    'variant_option_deleted',
                    null,
                    $deletedOption->name,
                    null,
                    "Variant option '{$deletedOption->name}' and all its values were deleted"
                );
                foreach ($deletedOption->values as $value) {
                    if ($value->image) {
                        Storage::disk('public')->delete($value->image);
                    }
                }
                $deletedOption->delete();
            }
        }
    }

    protected function logVariantChanges(EcommerceProduct $product, EcommerceProductVariant $variant, array $newData, string $optionName)
    {
        $fieldsToTrack = [
            'name' => 'Name',
            'price' => 'Price',
            'sale_price' => 'Sale Price',
            'capital_price' => 'Capital Price',
            'stock' => 'Stock',
            'is_active' => 'Active Status',
        ];

        foreach ($fieldsToTrack as $field => $label) {
            $oldValue = $variant->{$field};
            $newValue = $newData[$field] ?? null;

            if ($field === 'is_active') {
                $oldValue = (bool) $oldValue;
                $newValue = (bool) $newValue;
            }

            if (in_array($field, ['price', 'sale_price', 'capital_price', 'stock'])) {
                $oldValue = (float) $oldValue;
                $newValue = (float) ($newValue ?? 0);
            }

            if ($oldValue != $newValue) {
                $oldDisplay = $field === 'is_active' ? ($oldValue ? 'Yes' : 'No') : $oldValue;
                $newDisplay = $field === 'is_active' ? ($newValue ? 'Yes' : 'No') : $newValue;

                EcommerceProductLog::logChange(
                    $product->id,
                    'variant_updated',
                    $field,
                    $oldDisplay,
                    $newDisplay,
                    "Variant '{$optionName}: {$variant->name}' {$label} changed from '{$oldDisplay}' to '{$newDisplay}'"
                );
            }
        }
    }

    public function destroy(EcommerceProduct $ecommerce_product)
    {
        $product = $ecommerce_product;

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        foreach ($product->variantOptions as $option) {
            foreach ($option->values as $value) {
                if ($value->image) {
                    Storage::disk('public')->delete($value->image);
                }
            }
        }

        $product->delete();

        return redirect()->route('ecommerce_product.index')
            ->with('success', 'Product deleted successfully.');
    }
}