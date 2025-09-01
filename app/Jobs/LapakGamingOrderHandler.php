<?php

namespace App\Jobs;

use App\Data\LapakGaming\OrderRequestPayload;
use App\Models\Order;
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
        $token = config('array.lapakgaming.token');
        $baseUrl = config('array.lapakgaming.url');
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

        // Success response
        // -----------------
        // {
        //   "code": "SUCCESS",
        //   "data": {
        //     "tid": "R161582713591477186",
        //     "total_price": 33120
        //   }
        // }
        //


        // Test data to simulate each status
        // -------------------------
        // SUCCESS
        // product_code : ML78_8-S2

        // SUCCESS (for voucher)
        // product_code : VCGS330-S22

        // PRICE_NOT_MATCH
        // product_code : ML78_8-S2
        // price : 999999

        // PRODUCT_NOT_FOUND
        // product_code : ASD

        // PRODUCT_EMPTY
        // product_code : ML156_16-S42

        // PROVIDER_NOT_FOUND
        // product_code : ML234_23-S2

        // PROVIDER_INACTIVE
        // product_code : ML625_81-S2

        // INSUFFICIENT_BALANCE
        // product_code : ML7740_1548-S42

        // SUCCESS with pending order
        // product_code : ML4649_883-S42

        $response = Http::withToken($token)
            ->timeout(15)
            ->post($orderUrl, $payloadArray);

        if ($response->failed()) {
            throw new \Exception("LapakGaming API call failed: " . $response->body());
        }

        $responseJson = $response->json();

        if ($responseJson['code'] === 'SUCCESS') {
            Log::channel('lapakgaming')->info("Order {$this->order->id} successfully forwarded to LapakGaming.");

            $order->provider_ref = $responseJson['data']['tid'];
            $order->save();
        } else {
            Log::channel('lapakgaming')->error("Order {$this->order->id} error: " . $responseJson['code']);
        }

        // update order provider ref
    }

    /**
     * Handle a job failure after all retries.
     */
    public function failed(Throwable $exception): void
    {
        Log::channel('lapakgaming')->error("LapakGamingOrderHandler failed for Order {$this->order->id}: " . $exception->getMessage());
    }
}
