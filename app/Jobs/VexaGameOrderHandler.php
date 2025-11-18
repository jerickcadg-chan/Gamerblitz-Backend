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
        $order = $this->order;
        $productItem = $order->productItem;

        $baseUrl = Setting::getByKey('vexagame_api_url');
        $token   = Setting::getByKey('vexagame_api_token');
        $orderUrl = rtrim($baseUrl, '/') . '/v2/transaction';

        $payload = new OrderRequestData(
            code: $productItem->code,
            customer_no: $productItem->product->code === 'ML'
                ? implode('', $order->cust_account_array)
                : json_encode($order->cust_account_array),
            qty: $order->qty,
            partner_ref_id: $order->code
        );

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post($orderUrl, $payload->toArray());

            $orderResponse = $response->json();
            $httpStatus = $response->status();
            $code = (string)($orderResponse['code'] ?? '');
            $payloadData = $orderResponse['payload'] ?? [];

            // Jika HTTP 400 → cek exist_code
            if ($httpStatus === 400) {
                $existProviderCode = $payloadData['exist_code'] ?? null;

                if ($existProviderCode) {
                    // Update provider_ref jika sudah ada
                    $order->provider_ref = $existProviderCode;
                    $order->save();

                    Log::channel('vexagame')->notice(
                        "⚠️ Order {$order->id} failed with 400 but exist_code found. provider_ref updated."
                    );
                } else {
                    $orderService->updateStatus($order, StatusConst::DELAY, json_encode($orderResponse));

                    Log::channel('vexagame')->error(
                        "❌ Order {$order->id} failed with 400",
                        ['response' => $orderResponse]
                    );
                }
            }
            // Jika API JSON code 200 → sukses
            elseif ($code === '200') {
                $order->provider_ref = $payloadData['code'] ?? '';
                $order->save();

                Log::channel('vexagame')->notice(
                    "✅ Order {$order->id} successfully forwarded to VexaGame."
                );
            }
            // Lainnya → DELAY
            else {
                $orderService->updateStatus($order, StatusConst::DELAY, json_encode($orderResponse));

                Log::channel('vexagame')->error(
                    "❌ Order {$order->id} failed with code: {$code}",
                    ['response' => $orderResponse]
                );
            }
        } catch (\Illuminate\Http\Client\RequestException $e) {
            // Gagal koneksi / timeout
            Log::channel('vexagame')->warning(
                "⚠️ Order {$order->id} request failed: {$e->getMessage()}"
            );
            $orderService->updateStatus($order, StatusConst::ON_PROCESS, $e->getMessage());
        } catch (Exception $e) {
            // Error lain, misal balance empty
            Log::channel('vexagame')->error(
                "💥 Exception while processing order {$order->id}: {$e->getMessage()}"
            );
            $orderService->updateStatus($order, StatusConst::DELAY, $e->getMessage());
        }
    }
}
