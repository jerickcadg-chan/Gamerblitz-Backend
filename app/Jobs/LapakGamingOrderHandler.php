<?php

namespace App\Jobs;

use App\Data\LapakGaming\OrderRequestPayload;
use App\Data\LapakGaming\OrderResponse;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class LapakGamingOrderHandler implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var Order
     */
    public $order;

    /**
     * Number of times to attempt before failing.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Seconds to wait before retrying.
     *
     * @var int|array
     */
    public $backoff = [10, 30, 60]; // 1st retry after 10s, 2nd after 30s, 3rd after 60s

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $token = Setting::getByKey(Setting::KEY_LAPAKGAMING_API_TOKEN);
        $baseUrl = Setting::getByKey(Setting::KEY_LAPAKGAMING_API_URL);
        $orderUrl = $baseUrl . '/api/order';

        $order = $this->order;
        $productItem = $order->productItem;

        $payload = new OrderRequestPayload(
            count_order: $order->qty,
            product_code: $productItem->code,
            partner_reference_id: $order->code,
            // TODO: this may return price mismatch if the price is not up to date, in that case we need to sync the price immediately for this product item.
            // price: $order->capital,
        );

        $payload->fill($order->cust_account_array);

        $payloadArray = collect($payload)->filter()->toArray();

        Log::channel('lapakgaming')->info("LapakGamingOrderHandler attempt forwarding order", $payloadArray);

        $response = Http::withToken($token)
            ->timeout(15)
            ->post($orderUrl, $payloadArray);

        if ($response->failed()) {
            throw new \Exception("LapakGaming API call failed: " . $response->body());
        }

        $orderResponse = OrderResponse::from($response->json);

        if ($orderResponse->code === 'SUCCESS') {
            Log::channel('lapakgaming')->info("Order {$this->order->id} successfully forwarded to LapakGaming.");

            $order->provider_ref = $orderResponse->data->tid;
            $order->save();
        } else {
            Log::channel('lapakgaming')->error("Order {$this->order->id} error: " . $orderResponse->code);
        }
    }

    /**
     * Handle a job failure after all retries.
     */
    public function failed(Throwable $exception): void
    {
        Log::channel('lapakgaming')->error("LapakGamingOrderHandler failed for Order {$this->order->id}: " . $exception->getMessage());
    }
}
