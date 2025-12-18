<?php

namespace App\Console\Commands;

use App\Constants\ProviderConstant;
use App\Constants\StatusConst;
use App\Jobs\DynastyDgsOrderHandler;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckDynastyDgsTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-dynasty-dgs-trx';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to check transaction from dynasty dgs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::where('provider', ProviderConstant::DYNASTY_DGS)
            ->where('status', StatusConst::ON_PROCESS)
            ->get();

        foreach ($orders as $order) {
            $orderTime = Carbon::parse($order->created_at);

            if (Carbon::now()->diffInMinutes($orderTime) >= 2) {
                DynastyDgsOrderHandler::dispatch($order);
            }
        }
    }
}
