<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Console\Command;

class ReprocessVexagameOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vexagame:reprocess-order {order_code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reprocess a Vexagame order manually by order code.';

    /**
     * Execute the console command.
     */
    public function handle(OrderService $orderService)
    {
        $orderCode = $this->argument('order_code');

        $order = Order::where('code', $orderCode)->first();

        if (!$order) {
            $this->error("❌ Order with code {$orderCode} not found.");
            return Command::FAILURE;
        }

        $this->info("🔄 Reprocessing order: {$order->code}...");

        try {
            $orderService->processOrder($order);
            $this->info("✅ Order {$order->code} successfully reprocessed.");
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("⚠️ Failed to reprocess order: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}