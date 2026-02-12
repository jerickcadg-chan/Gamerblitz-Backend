<?php

namespace App\Http\Controllers\Api;

use App\Constants\StatusConst;
use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Order;
use App\Models\Setting;
use App\Services\BalanceService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class VexaGameController extends Controller
{
    /**
     * Handle VexaGame order callback.
     * 
     * @param Request $request
     * @param OrderService $orderService
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderCallback(Request $request, OrderService $orderService)
    {
        $authHeader = $request->header('Authorization');
        $providedToken = trim(str_replace('Bearer', '', $authHeader));

        if ($providedToken !== Setting::getByKey('vexagame_callback_token')) {
            return response()->json([
                'message' => 'FAILED',
                'reason'  => 'Invalid callback token',
            ], 404);
        }

        $log = Log::channel('vexagame');
        $payload = $request->all();

        try {
            $log->notice('📩 VexaGame callback received', $payload);

            $order = Order::query()
                ->where('provider_ref', $payload['code'] ?? null)
                ->first();

            if (!$order) {
                $log->error('❌ Callback received but order not found', [
                    'reference_id' => $payload['code'] ?? null,
                ]);

                return response()->json([
                    'message' => 'FAILED',
                    'reason'  => 'Invalid ref id',
                ], 404);
            }

            $status = strtoupper($payload['status'] ?? '');
            $note   = json_encode($payload);

            switch ($status) {
                case 'SUKSES':
                    if ($order->status != StatusConst::SUCCESS) {
                        $this->handleSuccess($order, $payload, $orderService, $note);
                    }
                    break;

                case 'GAGAL':
                case 'REFUNDED':
                    if ($order->status != StatusConst::FAILED) {
                        $this->handleRefund($order, $payload, $orderService, $note);
                    }
                    break;

                case 'DELAY':
                case 'DALAM_PROSES':
                    $orderService->updateStatus($order, StatusConst::DELAY, $note);
                    $log->warning('⚠️ Order still pending', [
                        'order_id' => $order->id,
                        'payload'  => $payload,
                    ]);
                    break;

                default:
                    $log->warning('⚠️ Unknown status in callback', [
                        'order_id' => $order->id,
                        'status'   => $status,
                        'payload'  => $payload,
                    ]);
                    break;
            }

            return response()->json(['message' => 'SUCCESS'], 200);
        } catch (Throwable $e) {
            $log->error('💥 Callback processing failed', [
                'error'   => $e->getMessage(),
                'payload' => $payload,
            ]);

            return response()->json([
                'message' => 'FAILED',
                'reason'  => 'Internal server error',
            ], 500);
        }
    }

    /**
     * Handle successful transaction callback.
     * 
     * @param Order $order
     * @param array $payload
     * @param OrderService $orderService
     * @param string $note
     * 
     * @return void
     */
    private function handleSuccess(Order $order, array $payload, OrderService $orderService, string $note): void
    {
        $transactions = collect($payload);

        $order->serial_number = $transactions->get('sn', '');
        $order->note = $transactions->get('description', '');
        $order->save();

        $orderService->updateStatus($order, StatusConst::SUCCESS, $note);

        Log::channel('vexagame')->notice("✅ Order {$order->code} marked as SUCCESS");
    }

    /**
     * Handle refunded transaction callback.
     * 
     * @param Order $order
     * @param array $payload
     * @param OrderService $orderService
     * @param string $note
     * 
     * @return void
     */
    private function handleRefund(Order $order, array $payload, OrderService $orderService, string $note): void
    {
        $transactions = collect($payload);

        $order->note = $transactions->get('description', '');
        $order->save();

        $orderService->updateStatus($order, StatusConst::FAILED, $note);

        if ($order->user && $order->user->balance) {
            $balance = Balance::firstWhere('user_id', $order->user_id);

            if ($balance) {
                BalanceService::update($balance, [
                    'balanceable_type' => Order::class,
                    'balanceable_id'   => $order->id,
                    'amount'           => $order->turnover,
                    'description'      => "Refund {$order->code}",
                ]);

                Log::channel('vexagame')->notice("💰 Balance refunded for order {$order->code}");
            }
        }
    }
}
