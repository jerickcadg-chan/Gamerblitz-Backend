<?php

namespace App\Http\Controllers\Api;

use App\Models\EcommerceCategory;
use App\Models\EcommerceProduct;
use App\Models\EcommerceProductVariant;
use App\Models\EcommerceOrder;
use App\Models\EcommerceOrderItem;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;


class EcommerceApiController extends Controller
{
    /**
     * Get all active eCommerce categories
     */
    public function categories()
    {
        $categories = EcommerceCategory::where('is_active', true)
            ->withCount(['products' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('name')
            ->get();

        return response()->json([
            'method' => 'GET',
            'code' => 200,
            'message' => 'Success',
            'payload' => $categories,
        ]);
    }

    /**
     * Get all active eCommerce products
     */
    public function products(Request $request)
    {
        $query = EcommerceProduct::with(['category', 'variantOptions.values'])
            ->where('is_active', true);

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by category slug
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter featured
        if ($request->has('featured')) {
            $query->where('is_featured', true);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sortBy = $request->get('sort', 'newest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderByRaw('COALESCE(sale_price, price) ASC');
                break;
            case 'price_high':
                $query->orderByRaw('COALESCE(sale_price, price) DESC');
                break;
            case 'name':
                $query->orderBy('name');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate($request->get('per_page', 12));

        // Transform products
        $transformed = $products->getCollection()->map(function ($product) {
            return $this->transformProduct($product);
        });

        return response()->json([
            'method' => 'GET',
            'code' => 200,
            'message' => 'Success',
            'payload' => [
                'data' => $transformed,
                'meta' => [
                    'pagination' => [
                        'total' => $products->total(),
                        'count' => $products->count(),
                        'per_page' => $products->perPage(),
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'has_more_pages' => $products->hasMorePages(),
                        'from' => $products->firstItem(),
                        'to' => $products->lastItem(),
                    ],
                ],
            ],
        ]);
    }

    /**
     * Get single product by slug
     */
    public function product($slug)
    {
        $product = EcommerceProduct::with(['category', 'variantOptions.values'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'method' => 'GET',
                'code' => 404,
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'method' => 'GET',
            'code' => 200,
            'message' => 'Success',
            'payload' => $this->transformProduct($product, true),
        ]);
    }

    /**
     * Transform product for API response
     */
    private function transformProduct(EcommerceProduct $product, bool $detailed = false)
    {
        $data = [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'price' => (float) $product->price,
            'sale_price' => $product->sale_price ? (float) $product->sale_price : null,
            'stock' => $product->stock,
            'track_stock' => $product->track_stock,
            'image' => $product->image ? asset('storage/' . $product->image) : null,
            'is_featured' => $product->is_featured,
            'meta_title' => $product->meta_title,
            'meta_description' => $product->meta_description,
            'meta_keywords' => $product->meta_keywords,
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
            ] : null,
            'has_variants' => $product->variantOptions->count() > 0,
        ];

        // Include price range and discount if has variants
        if ($product->variantOptions->count() > 0) {
            $activeValues = $product->variantOptions
                ->flatMap(fn($opt) => $opt->values)
                ->filter(fn($v) => $v->is_active);

            if ($activeValues->count() > 0) {
                $salePrices = $activeValues->map(fn($v) => $v->sale_price ?? $v->price);
                $originalPrices = $activeValues->map(fn($v) => (float) $v->price);

                $data['price_range'] = [
                    'min' => (float) $salePrices->min(),
                    'max' => (float) $salePrices->max(),
                ];

                // Calculate max discount percentage from variants
                $maxDiscount = 0;
                foreach ($activeValues as $value) {
                    if ($value->sale_price && $value->sale_price < $value->price) {
                        $discount = round((($value->price - $value->sale_price) / $value->price) * 100);
                        $maxDiscount = max($maxDiscount, $discount);
                    }
                }
                if ($maxDiscount > 0) {
                    $data['max_discount_percent'] = $maxDiscount;
                }
            }
        }

        // Include variant details for detailed view
        if ($detailed) {
            $data['variant_options'] = $product->variantOptions->map(function ($option) {
                return [
                    'id' => $option->id,
                    'name' => $option->name,
                    'values' => $option->values->filter(fn($v) => $v->is_active)->map(function ($value) {
                        return [
                            'id' => $value->id,
                            'name' => $value->name,
                            'price' => (float) $value->price,
                            'sale_price' => $value->sale_price ? (float) $value->sale_price : null,
                            'stock' => $value->stock,
                            'image' => $value->image ? asset('storage/' . $value->image) : null,
                        ];
                    })->values(),
                ];
            });
        }

        return $data;
    }

    /**
     * Create a new order with payment integration
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string|max:100',
            'shipping_province' => 'required|string|max:100',
            'shipping_postal_code' => 'required|string|max:10',
            'shipping_notes' => 'nullable|string',
            'payment_method' => 'required|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:ecommerce_products,id',
            'items.*.variant_id' => 'nullable|exists:ecommerce_product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $orderItems = [];
            $itemNames = [];

            foreach ($request->items as $item) {
                $product = EcommerceProduct::findOrFail($item['product_id']);
                $variant = null;
                $price = $product->sale_price ?? $product->price;
                $variantName = null;

                if (!empty($item['variant_id'])) {
                    $variant = EcommerceProductVariant::findOrFail($item['variant_id']);
                    $price = $variant->sale_price ?? $variant->price;
                    $variantName = $variant->name;

                    // Check stock
                    if ($variant->stock < $item['quantity']) {
                        throw new \Exception("Insufficient stock for {$product->name} - {$variantName}");
                    }
                } else {
                    // Check product stock
                    if ($product->track_stock && $product->stock < $item['quantity']) {
                        throw new \Exception("Insufficient stock for {$product->name}");
                    }
                }

                $itemSubtotal = $price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'product_name' => $product->name,
                    'variant_name' => $variantName,
                    'price' => $price,
                    'capital_price' => $variant ? ($variant->capital_price ?? 0) : ($product->capital_price ?? 0),
                    'quantity' => $item['quantity'],
                    'subtotal' => $price * $item['quantity'],
                ];

                // Build item name for payment order
                $itemName = $product->name;
                if ($variantName) {
                    $itemName .= ' - ' . $variantName;
                }
                $itemName .= ' (x' . $item['quantity'] . ')';
                $itemNames[] = $itemName;
            }

            // Create ecommerce order
            $ecommerceOrder = EcommerceOrder::create([
                'order_number' => EcommerceOrder::generateOrderNumber(),
                'user_id' => $user->id,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_province' => $request->shipping_province,
                'shipping_postal_code' => $request->shipping_postal_code,
                'shipping_notes' => $request->shipping_notes,
                'payment_method' => $request->payment_method,
                'subtotal' => $subtotal,
                'shipping_fee' => 0,
                'discount' => 0,
                'total' => $subtotal,
                'status' => 'pending',
            ]);

            // Create order items and reduce stock
            foreach ($orderItems as $item) {
                $ecommerceOrder->items()->create($item);

                // Reduce stock
                if (!empty($item['variant_id'])) {
                    EcommerceProductVariant::where('id', $item['variant_id'])
                        ->decrement('stock', $item['quantity']);
                } else {
                    $product = EcommerceProduct::find($item['product_id']);
                    if ($product->track_stock) {
                        $product->decrement('stock', $item['quantity']);
                    }
                }
            }

            // Create payment Order record for payment gateway integration
            $paymentMethod = PaymentMethod::where('slug', $request->payment_method)->first();

            if (!$paymentMethod) {
                // Fallback: find by name (case-insensitive)
                $paymentMethod = PaymentMethod::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($request->payment_method) . '%'])->first();
            }

            // Generate payment order code
            $baseInvCode = Setting::getByKey('base_inv_code') ?? 'GPDS';
            $paymentCode = $baseInvCode . date('ymd') . strtoupper(substr(uniqid(), -5));

            // Get a default product_item_id (required field) - use first product item or 1
            $defaultProductItemId = DB::table('product_items')->first()?->id ?? 1;

            // Create Order for payment processing (same as top-up orders)
            $paymentOrderId = DB::table('orders')->insertGetId([
                'code' => $paymentCode,
                'user_id' => $user->id,
                'ecommerce_order_id' => $ecommerceOrder->id,
                'product_item_id' => $defaultProductItemId,
                'payment_method_id' => $paymentMethod?->id,
                'cust_email' => $request->customer_email,
                'cust_phone_number' => $request->customer_phone,
                'cust_account' => $request->customer_name,
                'provider' => 'ecommerce',
                'status' => 'pending',
                'qty' => array_sum(array_column($request->items, 'quantity')),
                'price' => $subtotal,
                'capital' => 0,
                'turnover' => $subtotal,
                'admin_fee' => 0,
                'discount_price' => 0,
                'total_price' => $subtotal,
                'total_income' => $subtotal,
                'currency_code' => 'PHP',
                'converted_currency_code' => 'PHP',
                'exchange_rate' => 1,
                'converted_price' => $subtotal,
                'converted_capital' => 0,
                'converted_turnover' => $subtotal,
                'converted_admin_fee' => 0,
                'converted_discount_price' => 0,
                'converted_total_price' => $subtotal,
                'converted_total_income' => $subtotal,
                'note' => 'Ecommerce Order: ' . implode(', ', $itemNames),
                'additional_informations' => json_encode(['ecommerce_order_id' => $ecommerceOrder->id, 'items' => $itemNames]),
                'expired_at' => now()->addHours(1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Link ecommerce order to payment order
            $ecommerceOrder->update(['payment_order_id' => $paymentOrderId]);

            // Call payment service to generate payment URL based on vendor
            $paymentOrder = Order::find($paymentOrderId);

            if ($paymentMethod) {
                if ($paymentMethod->vendor === PaymentMethod::MPAY) {
                    app(\App\Services\MpayService::class)->createOrderMpayInvoice($paymentOrder);
                } elseif ($paymentMethod->vendor === PaymentMethod::XENDIT) {
                    $service = $paymentMethod->slug === 'CARDS'
                        ? \App\Services\XenditV2Service::class
                        : \App\Services\XenditService::class;
                    app($service)->createOrderXenditInvoice($paymentOrder);
                } elseif ($paymentMethod->vendor === PaymentMethod::HITPAY) {
                    app(\App\Services\HitpayService::class)->createOrderHitpayInvoice($paymentOrder);
                } elseif ($paymentMethod->vendor === PaymentMethod::BILLPLZ) {
                    app(\App\Services\BillplzService::class)->createOrderBillplzInvoice($paymentOrder);
                } elseif ($paymentMethod->vendor === PaymentMethod::CRYPTOMUS) {
                    app(\App\Services\CryptomusService::class)->createOrderCryptomusInvoice($paymentOrder);
                }

                // Refresh to get updated payment_url
                $paymentOrder->refresh();
            }

            // Log initial order status
            \App\Http\Controllers\EcommerceOrderController::logStatusChange($ecommerceOrder, 'pending', 'Order placed');

            DB::commit();

            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $ecommerceOrder->id,
                    'order_number' => $ecommerceOrder->order_number,
                    'total' => $ecommerceOrder->total,
                    'status' => $ecommerceOrder->status,
                    'payment_method' => $ecommerceOrder->payment_method,
                ],
                // Return payment code for redirect to payment page
                'payment_code' => $paymentCode,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Get user's orders
     */
    public function getUserOrders(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $orders = EcommerceOrder::where('user_id', $user->id)
            ->with('items')
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => $orders->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'total' => $orders->total(),
                ],
            ],
        ]);
    }

    /**
     * Get single order by order number
     */
    public function getOrder(Request $request, $orderNumber)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $order = EcommerceOrder::where('order_number', $orderNumber)
            ->where('user_id', $user->id)
            ->with('items')
            ->firstOrFail();

        return response()->json($order);
    }

    /**
     * Check if shop is under maintenance
     */
    public function checkMaintenance(Request $request)
    {
        $isAdmin = false;

        // Check if user is logged in and has admin role
        if ($request->user()) {
            $user = $request->user();
            $isAdmin = $user->hasRole(['Super Admin', 'Admin', 'Staff']) ||
                       $user->hasAnyPermission(['View Ecommerce Order', 'View Ecommerce Product']);
        }

        $maintenanceMode = Setting::where('key', 'ecommerce_shop_maintenance')->value('value') === '1';

        return response()->json([
            'maintenance' => $maintenanceMode && !$isAdmin,
            'is_admin' => $isAdmin
        ]);
    }
}