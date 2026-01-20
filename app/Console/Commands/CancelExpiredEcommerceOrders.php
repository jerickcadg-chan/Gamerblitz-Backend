<?php

namespace App\Console\Commands;

use App\Models\EcommerceOrder;
use App\Models\EcommerceProduct;
use App\Models\EcommerceProductVariant;
use App\Models\EcommerceOrderStatusHistory;
use App\Models\Order;
use App\Constants\StatusConst;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CancelExpiredEcommerceOrders extends Command
{
    protected $signature = 'ecommerce:cancel-expired-orders';
    protected $description = 'Cancel ecommerce orders that have been pending for more than 1 hour';

    public function handle()
	{
    $expiredOrders = EcommerceOrder::where('status', 'pending')
        ->where('created_at', '<', now()->subHour())
        ->whereHas('paymentOrder.paymentMethod', function ($query) {
            $query->where('slug', '!=', 'COD');
        })
        ->get();

        $count = 0;
        foreach ($expiredOrders as $order) {
            // Restore stock
            $this->restoreStock($order);

            // Update ecommerce order status to cancelled
            $order->update(['status' => 'cancelled']);

            // Also update the linked payment order to expired
            if ($order->payment_order_id) {
                Order::where('id', $order->payment_order_id)
                    ->update(['status' => StatusConst::EXPIRED]);
            }

            // Log status change
            EcommerceOrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'cancelled',
                'note' => 'Auto-cancelled: Payment not received within 1 hour',
                'user_id' => null,
            ]);

            $count++;
            Log::info("Ecommerce order {$order->order_number} auto-cancelled due to payment timeout");
        }

        $this->info("Cancelled {$count} expired ecommerce orders.");
        return Command::SUCCESS;
    }

    private function restoreStock(EcommerceOrder $order): void
    {
        $order->load('items');

        foreach ($order->items as $item) {
            if ($item->variant_id) {
                EcommerceProductVariant::where('id', $item->variant_id)
                    ->increment('stock', $item->quantity);
            } else {
                $product = EcommerceProduct::find($item->product_id);
                if ($product && $product->track_stock) {
                    $product->increment('stock', $item->quantity);
                }
            }
        }
    }
}
