<?php

namespace App\Http\Controllers;

use App\Models\EcommerceOrder;
use App\Models\EcommerceProduct;
use App\Models\EcommerceProductVariant;
use App\Models\EcommerceOrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EcommerceOrderController extends Controller
{
    private string $title;

    public function __construct()
    {
        $this->title = 'eCommerce Order';

        $this->middleware(['permission:View Ecommerce Order'])->only('index', 'show');
        $this->middleware(['permission:Edit Ecommerce Order'])->only('edit', 'update');
    }

    public function index()
    {
        $orders = EcommerceOrder::with(['user', 'items'])
            ->latest()
            ->when(request('code'), function ($query) {
                return $query->where('order_number', 'like', '%' . request('code') . '%');
            })
            ->when(request('status'), function ($query) {
                return $query->where('status', request('status'));
            })
            ->paginate();

        $title = $this->title;
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        return view('ecommerce.orders.index', compact('orders', 'title', 'statuses'));
    }

    public function show(EcommerceOrder $ecommerce_order)
    {
        $order = $ecommerce_order->load(['user', 'items.product', 'paymentOrder.paymentMethod', 'statusHistories']);
        $title = $this->title;
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        return view('ecommerce.orders.show', compact('order', 'title', 'statuses'));
    }

public function update(Request $request, EcommerceOrder $ecommerce_order)
{
    $request->validate([
        'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        'tracking_number' => 'nullable|string|max:255',
        'admin_notes' => 'nullable|string',
        'payment_status' => 'nullable|in:pending,success,expired,failed',
    ]);

    $oldStatus = $ecommerce_order->status;
    $newStatus = $request->status;

    // Restore stock when order is cancelled (only if not already cancelled)
    if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
        self::restoreStock($ecommerce_order);

        // Also expire the payment order
        if ($ecommerce_order->payment_order_id) {
            \App\Models\Order::where('id', $ecommerce_order->payment_order_id)
                ->update(['status' => \App\Constants\StatusConst::EXPIRED]);
        }
    }

    // Mark payment order as SUCCESS when ecommerce order is delivered
    if ($newStatus === 'delivered' && $oldStatus !== 'delivered') {
        if ($ecommerce_order->payment_order_id) {
            \App\Models\Order::where('id', $ecommerce_order->payment_order_id)
                ->update(['status' => \App\Constants\StatusConst::SUCCESS]);
        }
        $ecommerce_order->delivered_at = now();
    }

    // Mark shipped_at timestamp
    if ($newStatus === 'shipped' && $oldStatus !== 'shipped') {
        $ecommerce_order->shipped_at = now();
    }

    $ecommerce_order->update([
        'status' => $request->status,
        'tracking_number' => $request->tracking_number,
        'admin_notes' => $request->admin_notes,
    ]);

    // Log status change if status changed
    if ($oldStatus !== $newStatus) {
        EcommerceOrderStatusHistory::create([
            'order_id' => $ecommerce_order->id,
            'status' => $newStatus,
            'note' => 'Order status updated by admin',
            'user_id' => auth()->id(),
        ]);
    }

    // Handle payment status update
    if ($request->filled('payment_status') && $ecommerce_order->payment_order_id) {
        $paymentOrder = \App\Models\Order::find($ecommerce_order->payment_order_id);
        if ($paymentOrder && $paymentOrder->status !== $request->payment_status) {
            $oldPaymentStatus = $paymentOrder->status;
            $paymentOrder->update(['status' => $request->payment_status]);

            // Log payment status change
            EcommerceOrderStatusHistory::create([
                'order_id' => $ecommerce_order->id,
                'status' => "payment:{$request->payment_status}",
                'note' => "Payment status changed from '{$oldPaymentStatus}' to '{$request->payment_status}'",
                'user_id' => auth()->id(),
            ]);
        }
    }

    return redirect()->route('ecommerce_order.show', $ecommerce_order->id)
        ->with('success', 'Order updated successfully.');
}

    // Add static method to restore stock for cancelled orders (reusable by scheduled command)
    public static function restoreStock(EcommerceOrder $order): void
    {
        $order->load('items');

        foreach ($order->items as $item) {
            if ($item->variant_id) {
                // Restore variant stock
                EcommerceProductVariant::where('id', $item->variant_id)
                    ->increment('stock', $item->quantity);
            } else {
                // Restore product stock
                $product = EcommerceProduct::find($item->product_id);
                if ($product && $product->track_stock) {
                    $product->increment('stock', $item->quantity);
                }
            }
        }
    }

    // <CHANGE> Add static method to log status changes (reusable by API and scheduled commands)
    public static function logStatusChange(EcommerceOrder $order, string $status, ?string $note = null, ?int $userId = null): void
    {
        EcommerceOrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $status,
            'note' => $note,
            'user_id' => $userId,
        ]);
    }
}