<?php

namespace App\Http\Controllers\Api;

use App\Constants\StatusConst;
use App\Data\LapakGaming\OrderCallbackPayload;
use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Order;
use App\Services\BalanceService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class LapakGamingController extends Controller
{
    public function orderCallback(Request $request, OrderService $orderService): Response
    {
        $log = Log::channel('lapakgaming');

        try {
            $log->notice("Order callback received", $request->toArray());

            $payload = OrderCallbackPayload::from($request->all());

            $order = Order::query()
                ->where('code', $payload->data->reference_id)
                ->where('provider_ref', $payload->data->tid)
                ->first();

            if (!$order) {
                $log->error("Order callback received but order not found", [
                    'reference_id' => $payload->data->reference_id,
                    'tid' => $payload->data->tid,
                    'payload' => $payload->toArray(),
                ]);

                return response([
                    'message' => 'FAILED',
                    'reason' => 'Invalid ref id',
                ]);
            }

            $note = $payload->toJson();

            switch ($payload->data->status) {
                case "SUCCESS":
                    $transactions = collect($payload->data->transactions ?? []);

                    $order->serial_number = $transactions->pluck('voucher_code')
                        ->filter()
                        ->implode(',');

                    $order->note = $transactions->pluck('note')
                        ->filter()
                        ->implode(',');

                    $order->save();
                    $orderService->updateStatus($order, StatusConst::SUCCESS, $note);
                    break;
                case "REFUNDED":
                    $transactions = collect($payload->data->transactions ?? []);

                    $order->note = $transactions->pluck('note')
                        ->filter()
                        ->implode(',');

                    $order->save();
                    $orderService->updateStatus($order, StatusConst::FAILED, $note);
                    if ($order?->user?->balance) {
                        $balance = Balance::where('user_id', $order->user_id)->first();

                        BalanceService::update($balance, [
                            'balanceable_type' => Order::class,
                            'balanceable_id' => $order->id,
                            'amount' => $order->turnover,
                            'description' => "Refund $order->code"
                        ]);
                    }
                    break;
                case "PENDING":
                    $orderService->updateStatus($order, StatusConst::DELAY, $note);
                    $log->error("Order still pending on callback", [
                        'order_id' => $order->id,
                        'payload' => $payload->toArray(),
                    ]);
                    break;
                default:
                    break;
            }

            return response([
                'message' => 'SUCCESS',
            ], 200);
        } catch (\Throwable $e) {
            $log->error("Order callback processing failed", [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response([
                'message' => 'FAILED',
                'reason' => 'Internal server error',
            ], 500);
        }
    }
}
