<?php

namespace App\Jobs;

use App\Constants\StatusConst;
use App\Data\VexaGame\OrderRequestData;
use App\Models\Order;
use App\Models\Setting;
use App\Services\OrderService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VexaGameOrderHandler implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order) {}

    /**
     * Execute the job.
     */
    public function handle(OrderService $orderService): void
    {
        try {
            $baseUrl = Setting::getByKey('vexagame_api_url');
            $token   = Setting::getByKey('vexagame_api_token');
            $orderUrl = rtrim($baseUrl, '/') . '/v2/transaction';

            $order       = $this->order;
            $productItem = $order->productItem;

            $payload = new OrderRequestData(
                code: $productItem->code,
                customer_no: $productItem->product->code === 'ML'
                    ? implode('', $order->cust_account_array)
                    : json_encode($order->cust_account_array),
                qty: $order->qty,
                partner_ref_id: $order->code
            );

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])
                ->timeout(15)
                ->post($orderUrl, $payload->toArray());

            if ($response->failed()) {
                Log::channel('vexagame')->error(
                    "❌ Order {$order->id} failed",
                    ['response' => $response->body()]
                );

                throw new Exception("VexaGame API call failed: " . $response->body());
            }

            $orderResponse = $response->json();

            $code = (string)($orderResponse['code'] ?? '');
            $payloadData = $orderResponse['payload'] ?? [];

            if ($code == '200') {
                Log::channel('vexagame')->notice("✅ Order {$order->id} successfully forwarded to VexaGame.");

                $order->provider_ref = $payloadData['code'] ?? '';
                $order->save();
            } else {
                $orderService->updateStatus($order, StatusConst::DELAY, $code);

                Log::channel('vexagame')->error(
                    "❌ Order {$order->id} failed with code: {$code}",
                    ['response' => $orderResponse]
                );
            }
        } catch (Exception $e) {
            Log::channel('vexagame')->error("💥 Exception while processing order {$this->order->id}: {$e->getMessage()}");

            $orderService->updateStatus($this->order, StatusConst::DELAY, $e->getMessage());
        }
    }
}
