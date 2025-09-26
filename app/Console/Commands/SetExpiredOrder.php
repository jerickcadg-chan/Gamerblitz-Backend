<?php

namespace App\Console\Commands;

use App\Constants\StatusConst;
use Illuminate\Console\Command;
use App\Models\Order;
use App\Mail\SendOrderNotif;
use App\Services\OrderService;

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
        $orders = Order::where('status', StatusConst::PENDING)
            ->where('expired_at', '<=', now()->format('Y-m-d H:i:s'))
            ->get();

        if ($orders->count() > 0) {
            foreach ($orders as $order) {
                $orderService->updateStatus($order, StatusConst::EXPIRED);

                if ($order->cust_email) {
                    \Mail::to($order->cust_email)->send(new SendOrderNotif($order));
                }

                $this->info($order->code);
            }
        }
    }
}
