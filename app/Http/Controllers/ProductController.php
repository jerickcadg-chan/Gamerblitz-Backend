<?php

namespace App\Http\Controllers;

use App\Constants\CountryConstant;
use App\Constants\ProviderConstant;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Services\PictureService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private string $title;

    public function __construct()
    {
        $this->title = 'Product';

        $this->middleware(['permission:View Product'])->only('index', 'show');
        $this->middleware(['permission:Create Product'])->only(['create', 'store']);
        $this->middleware(['permission:Edit Product'])->only('edit', 'update');
        $this->middleware(['permission:Delete Product'])->only('destroy');
    }

    public function index()
    {
        $products = Product::latest()
            ->withCount([
                'productItems as product_items_count' => function ($q) {
                    $q->active();
                }
            ])
            ->when(request('name'), function ($query) {
                return $query->where('name', 'like', '%' . request('name') . '%');
            })
            ->paginate();

        $createLink = route('product.create');

        $title = $this->title;

        return view('products.index', compact('products', 'createLink', 'title'));
    }

    public function create()
    {
        $formAction = route('product.store');
        $indexLink = route('product.index');

        $title = $this->title;

        $supportProviders = explode(',', env('SUPPORTED_PROVIDER', 'lapakgaming'));

        $providers = ProviderConstant::AVAILABLE_PROVIDER;
        $countries = CountryConstant::all();

        return view('products.form', compact('providers', 'supportProviders', 'countries', 'formAction', 'indexLink', 'title'));
    }

    public function createFromLG(Request $request)
    {
        $lapakGamingCountry = $request->lapakgaming_country;
        $lapakGamingCode = $request->lapakgaming_code;

        if ($lapakGamingCountry && $lapakGamingCode) {
            $upperCountryCode = strtoupper($lapakGamingCountry);

            $lgCategories = collect(Cache::get("lapakgaming_categories_{$upperCountryCode}"));

            if (!$lgCategories) {
                toast('Data not found, please filter again and retry the process', 'error');
                return back();
            }

            $category = $lgCategories
                ->where('code', $lapakGamingCode)
                ->where('country_code', strtolower($lapakGamingCountry))
                ->first();

            if ($category && isset($category['servers'])) {
                unset($category['servers']);
            }

            if (!$category) {
                toast('Data not found, please filter again and retry the process', 'error');
                return back();
            }

            if (isset($category['forms']) && is_array($category['forms'])) {
                foreach ($category['forms'] as $key => $form) {

                    if (
                        isset($form['type']) &&
                        $form['type'] === 'option' &&
                        isset($form['options']) &&
                        count($form['options']) > 500
                    ) {
                        // ubah jadi text
                        $category['forms'][$key]['type'] = 'text';

                        // tambahkan placeholder dari option pertama
                        $category['forms'][$key]['placeholder'] = $form['options'][0]['value'] ?? 'Input value';

                        // hapus option biar gak berat
                        unset($category['forms'][$key]['options']);
                    }
                }
            }

            $defaults = [
                'name'             => $category['name'],
                'slug'             => Str::slug($category['name']),
                'code'             => $category['code'],
                'provider_code'    => $category['code'],
                'provider_country' => $upperCountryCode,
                'input_format'     => json_encode($category['forms']),
            ];

            return redirect()
                ->route('product.create')
                ->withInput($defaults);
        }

        toast('Failed to create from LapakGaming', 'error');
        return redirect()->back();
    }

    public function show(Product $product)
    {
        $editLink = route('product.edit', $product);
        $deleteLink = route('product.destroy', $product);
        $indexLink = route('product.index');

        $title = $this->title;

        return view('products.show', compact('product', 'editLink', 'indexLink', 'deleteLink', 'title'));
    }

    public function store(ProductRequest $request)
    {
        $pictureService = new PictureService();

        if ($request->hasFile('cover')) {
            $request['default_cover']   = $pictureService->insert($request->cover);
        }
        if ($request->hasFile('picture')) {
            $request['default_picture'] = $pictureService->insert($request->picture);
        }

        Product::create($request->all());

        toast(alert_created_text($this->title), 'success');

        return redirect()->route('product.index');
    }

    public function edit(Product $product)
    {
        dd($this->mapRequireInfo(
            [
                [
                    "name" => "Input1",
                    "description" => "User ID",
                    "selection" => []
                ],
                [
                    "name" => "Input2",
                    "description" => "Server ID",
                    "selection" => [
                        [
                            "name" => "Asia",
                            "code" => "os_asia"
                        ],
                        [
                            "name" => "Europe",
                            "code" => "os_euro"
                        ],
                        [
                            "name" => "America",
                            "code" => "os_usa"
                        ],
                        [
                            "name" => "TW, HK, MO",
                            "code" => "os_cht"
                        ]
                    ]
                ]
            ]
        ));
        $formAction = route('product.update', $product);
        $indexLink = route('product.index');

        $title = $this->title;

        $supportProviders = explode(',', env('SUPPORTED_PROVIDER', 'lapakgaming'));

        $providers = ProviderConstant::AVAILABLE_PROVIDER;
        $countries = CountryConstant::all();

        return view('products.form', compact('providers', 'supportProviders', 'countries', 'formAction', 'indexLink', 'product', 'title'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $pictureService = new PictureService();

        if ($request->hasFile('cover')) {
            $request['default_cover']   = $pictureService->insert($request->cover);
        }
        if ($request->hasFile('picture')) {
            $request['default_picture'] = $pictureService->insert($request->picture);
        }

        $product->update($request->all());

        toast(alert_updated_text($this->title), 'success');
        return redirect()->back();
    }

    public function destroy(Product $product)
    {
        DB::table('discount_product')
            ->where('productable_id', $product->id)
            ->where('productable_type', 'App\Models\Product')
            ->delete();

        $product->delete();

        toast(alert_deleted_text($this->title), 'success');

        return redirect()->route('product.index');
    }
}
