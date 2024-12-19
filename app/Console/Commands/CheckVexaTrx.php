<?php

namespace App\Console\Commands;

use App\Mail\SendOrderNotif;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class CheckVexaTrx extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:vexa-trx';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check vexa agen transaction status';

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
     */
    public function handle(OrderService $orderService)
    {
        $orders = Order::where('order_status', Order::INPROCESS)->get();

        foreach ($orders as $order) {
            if ($order->vexa_invoice) {
                $request = Http::asForm()->withHeaders([
                    'Authorization' => config('array.vexa.token'),
                    'Accept' => 'application/json'
                ])->get(config('array.vexa.url') . '/v2/transaction/'. $order->vexa_invoice)->collect();

                $response = json_decode($request->collect());

                $status = $response->payload->status;

                if ($status === "Sukses") {
                    $orderService->updateStatus($order, null, Order::DONE);

                    if ($order->cust_email) {
                        Mail::to($order->cust_email)->send(new SendOrderNotif($order));
                    }
                }
            }
        }
    }
}
