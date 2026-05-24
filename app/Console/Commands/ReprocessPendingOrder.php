<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Console\Command;
use App\Constants\StatusConst;

class ReprocessPendingOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:reprocess-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reprocess all Vexagame orders with status ON_PROCESS and null provider_ref.';

    /**
     * Execute the console command.
     */
    public function handle(OrderService $orderService)
    {
        $orders = Order::where('status', StatusConst::ON_PROCESS)
            ->whereNull('provider_ref')
            ->get();

        if ($orders->isEmpty()) {
            $this->info('✅ No pending orders found to reprocess.');
            return Command::SUCCESS;
        }

        $this->info("🔄 Found {$orders->count()} pending orders to reprocess...\n");

        foreach ($orders as $order) {
            $this->line("➡️ Reprocessing order: {$order->code}");

            try {
                $orderService->processOrder($order);
                $this->info("✅ Order {$order->code} successfully reprocessed.\n");
            } catch (\Throwable $e) {
                $this->error("⚠️ Failed to reprocess {$order->code}: {$e->getMessage()}\n");
            }
        }

        $this->info('🎉 All pending orders have been processed.');
        return Command::SUCCESS;
    }
}
