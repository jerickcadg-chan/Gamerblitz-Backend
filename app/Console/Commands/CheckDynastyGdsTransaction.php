<?php

namespace App\Console\Commands;

use App\Constants\ProviderConstant;
use App\Constants\StatusConst;
use App\Jobs\DynastyGdsOrderHandler;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckDynastyGdsTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-dynasty-gds-transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::where('provider', ProviderConstant::VEXAGAME)
            ->where('status', StatusConst::ON_PROCESS)
            ->get();

        foreach ($orders as $order) {
            $orderTime = Carbon::parse($order->created_at);

            if (Carbon::now()->diffInMinutes($orderTime) >= 2) {
                DynastyGdsOrderHandler::dispatch($order);
            }
        }
    }
}
