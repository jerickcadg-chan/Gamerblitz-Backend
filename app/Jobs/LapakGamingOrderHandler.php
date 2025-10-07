<?php

namespace App\Jobs;

use App\Constants\StatusConst;
use App\Data\LapakGaming\OrderRequestPayload;
use App\Data\LapakGaming\OrderResponse;
use App\Models\Order;
use App\Models\Setting;
use App\Services\OrderService;
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
    public function handle(OrderService $orderService): void
    {
        $token = Setting::getByKey(Setting::KEY_LAPAKGAMING_API_TOKEN);
        $baseUrl = Setting::getByKey(Setting::KEY_LAPAKGAMING_API_URL);
        $orderUrl = $baseUrl . '/api/order';

        $order = $this->order;
        $productItem = $order->productItem;

        $payload = new OrderRequestPayload(
            count_order: $order->qty,
            group_code: $productItem->code,
            partner_reference_id: $order->code,
            // TODO: this may return price mismatch if the price is not up to date, in that case we need to sync the price immediately for this product item.
            // price: $order->capital,
        );

        $payload->fill($order->cust_account_array);

        $payloadArray = collect($payload)->filter()->toArray();

        $response = Http::withToken($token)
            ->timeout(15)
            ->post($orderUrl, $payloadArray);

        if ($response->failed()) {
            throw new \Exception("LapakGaming API call failed: " . $response->body());
        }

        $orderResponse = OrderResponse::from($response->json());

        switch ($orderResponse->code) {
            case 'SUCCESS':
                Log::channel('lapakgaming')->notice("Order {$this->order->id} successfully forwarded to LapakGaming.");

                $order->provider_ref = $orderResponse->data->tid;
                $order->save();
                break;
            case 'PRODUCT_NOT_FOUND':
            case 'PRICE_NOT_MATCH':
            case 'PRODUCT_EMPTY':
            case 'PROVIDER_NOT_FOUND':
            case 'PROVIDER_INACTIVE':
            case 'INSUFFICIENT_BALANCE':
            default:
                $orderService->updateStatus($order, StatusConst::DELAY, $orderResponse->code);
                Log::channel('lapakgaming')->error("Order {$this->order->id} error: " . $orderResponse->code, $payloadArray);
                break;
        };
    }

    /**
     * Handle a job failure after all retries.
     */
    public function failed(Throwable $exception): void
    {
        Log::channel('lapakgaming')->error("LapakGamingOrderHandler failed for Order {$this->order->id}: " . $exception->getMessage());
        $orderService = app(OrderService::class);
        $orderService->updateStatus($this->order, StatusConst::PENDING, 'JOB_FAILED');
    }
}
