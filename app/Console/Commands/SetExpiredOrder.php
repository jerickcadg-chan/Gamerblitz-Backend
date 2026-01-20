<?php

namespace App\Console\Commands;

use App\Constants\StatusConst;
use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\EcommerceOrder;
use App\Mail\SendOrderNotif;
use App\Services\OrderService;
use App\Http\Controllers\EcommerceOrderController;

class SetExpiredOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expired:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set expired order';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(OrderService $orderService)
    {
        // Handle regular top-up orders
        $orders = Order::where('status', StatusConst::PENDING)
            ->where('expired_at', '<=', now()->format('Y-m-d H:i:s'))
            ->get();

        if ($orders->count() > 0) {
            foreach ($orders as $order) {
                $orderService->updateStatus($order, StatusConst::EXPIRED);

                if ($order->cust_email) {
                    \Mail::to($order->cust_email)->send(new SendOrderNotif($order));
                }

                $this->info('Expired order: ' . $order->code);
            }
        }

        // <CHANGE> Handle ecommerce orders - cancel after 1 hour and restore stock
        $ecommerceOrders = EcommerceOrder::where('status', 'pending')
            ->where('created_at', '<=', now()->subHour())
            ->get();

        if ($ecommerceOrders->count() > 0) {
            foreach ($ecommerceOrders as $ecommerceOrder) {
                // Restore stock before cancelling
                EcommerceOrderController::restoreStock($ecommerceOrder);

                // Update status to cancelled
                $ecommerceOrder->update(['status' => 'cancelled']);

                // Also update the linked payment order if exists
                if ($ecommerceOrder->payment_order_id) {
                    Order::where('id', $ecommerceOrder->payment_order_id)
                        ->update(['status' => StatusConst::EXPIRED]);
                }

                $this->info('Expired ecommerce order: ' . $ecommerceOrder->order_number);
            }
        }

        return 0;
    }
}