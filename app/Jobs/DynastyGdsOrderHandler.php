<?php

namespace App\Jobs;

use App\Constants\StatusConst;
use App\Models\Order;
use App\Services\DynastyGdsService;
use App\Services\OrderService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class DynastyGdsOrderHandler implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}
    
    /**
     * Execute the job.
     * 
     * @param DynastyGdsService $dynasty
     * @param OrderService $orderService
     * 
     * @return void
     */
    public function handle(DynastyGdsService $dynasty, OrderService $orderService): void
    {
        $order = $this->order;

        // Parse existing provider_ref -> array (trim each)
        $providerCodes = $order->provider_ref
            ? array_map('trim', explode(',', $order->provider_ref))
            : [];

        $itemStatuses   = []; // per item mapped status (StatusConst)
        $itemResponses  = []; // per item raw responses for debug/audit

        try {
            // Loop per item index (0 .. qty-1)
            for ($i = 0; $i < $order->qty; $i++) {
                $merchantRef = $order->code . '-' . $i;

                // If provider_ref already exists for this index -> just check
                if (!empty($providerCodes[$i])) {
                    $orderNo       = $providerCodes[$i];
                    $checkResponse = $dynasty->check($orderNo, $merchantRef);
                    $mappedStatus  = $this->mapStatusResponse($checkResponse);

                    $itemStatuses[$i]  = $mappedStatus;
                    $itemResponses[$i] = [
                        'action'   => 'check',
                        'orderNo'  => $orderNo,
                        'response' => $checkResponse,
                    ];

                    continue;
                }

                // provider_ref not exists -> create order
                $createResponse = $dynasty->order($order, $merchantRef);
                $mapCreate      = $this->mapOrderResponse($createResponse);

                // save create response for this item
                $itemResponses[$i] = [
                    'action'   => 'create',
                    'response' => $createResponse,
                ];

                if ($mapCreate !== StatusConst::SUCCESS) {
                    // create failed -> mark this item as DELAY
                    $itemStatuses[$i] = StatusConst::DELAY;
                    // keep providerCodes[$i] empty (no orderNo to save)
                    continue;
                }

                // created successfully -> grab orderNo and store it in provider_ref
                $orderNo = $createResponse['orderNo'] ?? null;

                if ($orderNo) {
                    $providerCodes[$i] = $orderNo;
                    // persist provider_ref immediately (so next job run will skip create)
                    $order->provider_ref = implode(',', $providerCodes);
                    $order->save();

                    // after create, do a check to get immediate status
                    $checkResponse = $dynasty->check($orderNo, $order->code);
                    $mappedStatus  = $this->mapStatusResponse($checkResponse);

                    $itemStatuses[$i]  = $mappedStatus;
                    $itemResponses[$i] = [
                        'action'   => 'create_and_check',
                        'orderNo'  => $orderNo,
                        'response' => $checkResponse,
                        'create'   => $createResponse,
                    ];
                } else {
                    // no orderNo returned -> treat as DELAY
                    $itemStatuses[$i] = StatusConst::DELAY;
                }
            }

            // Aggregate final status according to rules:
            // all SUCCESS -> SUCCESS
            // else if any ON_PROCESS -> ON_PROCESS
            // else if any DELAY -> DELAY
            // else default ON_PROCESS
            $finalStatus = $this->aggregateStatus($itemStatuses);

            // Save final/aggregate status and item responses as metadata
            $orderService->updateStatus(
                $order,
                $finalStatus,
                json_encode([
                    'items' => $itemResponses,
                ])
            );
        } catch (Exception $e) {
            Log::channel('dynasty_gds')->error(
                "💥 Exception while processing order {$order->id}: {$e->getMessage()}"
            );
            $orderService->updateStatus($order, StatusConst::DELAY, $e->getMessage());
        }
    }

    /**
     * Map create order response.
     * 
     * @param array $response
     * 
     * @return string
     */
    private function mapOrderResponse(array $response): string
    {
        $message = $response['message'] ?? null;

        $successMessages = [
            'Ok',
            'Merchant ref duplicated',
        ];

        if (in_array($message, $successMessages, true)) {
            return StatusConst::SUCCESS;
        }

        return StatusConst::DELAY;
    }

    /**
     * Map order status check.
     * 
     * @param array $response
     * 
     * @return string
     */
    private function mapStatusResponse(array $response): string
    {
        return match ($response['status'] ?? null) {
            'success'   => StatusConst::SUCCESS,
            'Created'   => StatusConst::SUCCESS,
            'pending'   => StatusConst::ON_PROCESS,
            'cancelled',
            'error'     => StatusConst::DELAY,
            default     => StatusConst::ON_PROCESS,
        };
    }

    /**
     * Aggregate per-item statuses into a final order status.
     *
     * Rules:
     * - If all items are SUCCESS -> SUCCESS
     * - Elseif any ON_PROCESS exists -> ON_PROCESS
     * - Elseif any DELAY exists -> DELAY
     * - Default -> ON_PROCESS
     *
     * @param array $itemStatuses indexed by item index
     * @return string
     */
    private function aggregateStatus(array $itemStatuses): string
    {
        if (empty($itemStatuses)) {
            return StatusConst::ON_PROCESS;
        }

        $hasDelay = in_array(StatusConst::DELAY, $itemStatuses, true);
        $hasOnProcess = in_array(StatusConst::ON_PROCESS, $itemStatuses, true);
        $allSuccess = count(array_unique($itemStatuses)) === 1
            && reset($itemStatuses) === StatusConst::SUCCESS;

        if ($hasDelay) {
            return StatusConst::DELAY;
        }

        if ($hasOnProcess) {
            return StatusConst::ON_PROCESS;
        }

        if ($allSuccess) {
            return StatusConst::SUCCESS;
        }

        return StatusConst::ON_PROCESS;
    }
}
