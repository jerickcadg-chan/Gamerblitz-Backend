<?php

namespace App\Console\Commands;

use App\Constants\ProviderConstant;
use App\Constants\StatusConst;
use App\Jobs\VexaGameOrderHandler;
use App\Models\Order;
use App\Models\Setting;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckVexagameTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-vexagame-trx';

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

        $now = Carbon::now();

        foreach ($orders as $order) {
            $orderTime = Carbon::parse($order->created_at);

            // Cek order sudah lewat 2 menit
            if ($now->diffInMinutes($orderTime) >= 2) {
                continue;
            }

            if ($order->provider_ref != null) {
                $baseUrl = Setting::getByKey('vexagame_api_url');
                $token   = Setting::getByKey('vexagame_api_token');
                $orderUrl = rtrim($baseUrl, '/') . '/v2/transaction';

                try {
                    $response = Http::withHeaders([
                        'Authorization' => $token,
                    ])->get("{$orderUrl}/{$order->provider_ref}");

                    if (!$response->successful()) {
                        Log::channel('vexagame')->warning(
                            "⚠️ Failed to fetch order status for {$order->id}: HTTP {$response->status()}"
                        );
                        continue;
                    }

                    $json = $response->json();
                    $status = $json['payload']['status'] ?? null;

                    if ($status === 'Sukses') {
                        $this->handleSuccess($order, $json['payload'], json_encode($json));
                    }
                } catch (\Illuminate\Http\Client\RequestException $e) {
                    Log::channel('vexagame')->warning(
                        "⚠️ Error fetching order {$order->id}: {$e->getMessage()}"
                    );
                }
            } else {
                // Jika provider_ref belum ada, dispatch job baru
                VexaGameOrderHandler::dispatch($order);
            }
        }
    }

    private function handleSuccess(Order $order, array $payload, string $note): void
    {
        $transactions = collect($payload);

        $order->serial_number = $transactions->get('sn', '');
        $order->note = $transactions->get('description', '');
        $order->save();

        app(OrderService::class)->updateStatus($order, StatusConst::SUCCESS, $note);

        Log::channel('vexagame')->notice("✅ Order {$order->code} marked as SUCCESS");
    }
}