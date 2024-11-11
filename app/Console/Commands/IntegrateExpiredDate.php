<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Carbon\Carbon;

class IntegrateExpiredDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'integrate:expired-date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set nullable expired date';

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
    public function handle()
    {
        $orders = Order::whereNull('expired_at')->get();

        foreach ($orders as $order) {
            $order->expired_at = Carbon::parse($order->created_at)->addHours(env('EXPIRED_HOURS'));
            $order->save();

            $this->info($order->code);
        }
    }
}
