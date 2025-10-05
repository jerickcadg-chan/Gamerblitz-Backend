<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HitpayService
{
    /**
     * @param Order $order
     *
     * @return array
     */
    public function createOrderHitpayInvoice(Order $order): array
    {
        $amount = $order->total_price;
        $externalId = $order->code;
        $method = $order->paymentMethod;

        $payload = [
            'email'            => $order->cust_email,
            'redirect_url'     => config('app.fe_url') . '/payment/' . $externalId,
            'reference_number' => $externalId,
            'webhook'          => route('callback.hitpay'),
            'currency'         => $method->currency_code,
            'amount'           => (int) $amount,
            'payment_methods'  => [$method->slug],
        ];

        $response = Http::withHeaders([
            'X-BUSINESS-API-KEY' => Setting::getByKey('hitpay_api_key'),
            'Content-Type'       => 'application/x-www-form-urlencoded',
            'X-Requested-With'   => 'XMLHttpRequest',
        ])->asForm()
            ->post(Setting::getByKey('hitpay_api_url') . "/payment-requests", $payload)
            ->json();

        Log::info('Hitpay payment requests response', $response);

        $order->payment_url = $response['url'] ?? null;
        $order->payment_code = $response['reference_number'] ?? null;
        $order->payment_id = $response['id'] ?? null;
        $order->save();

        return $response;
    }

    /**
     * @param Deposit $deposit
     *
     * @return array
     */
    public function createDepositHitpayInvoice(Deposit $deposit): array
    {
        $amount = ceil($deposit->total_amount);
        $externalId = $deposit->code;
        $method = $deposit->paymentMethod;

        $payload = [
            'email'            => $deposit->user->email,
            'redirect_url'     => config('app.fe_url').'/dashboard/deposit/' . $externalId,
            'reference_number' => $externalId,
            'webhook'          => route('callback.hitpay'),
            'currency'         => $method->currency_code,
            'amount'           => (float) $amount,
            'payment_methods'  => [$method->slug],
        ];

        $response = Http::withHeaders([
            'X-BUSINESS-API-KEY' => Setting::getByKey('hitpay_api_key'),
            'Content-Type'       => 'application/x-www-form-urlencoded',
            'X-Requested-With'   => 'XMLHttpRequest',
        ])->asForm()
            ->post(Setting::getByKey('hitpay_api_url') . "/payment-requests", $payload)
            ->json();

        $deposit->payment_url = $response['url'] ?? null;
        $deposit->payment_code = $response['reference_number'] ?? null;
        $deposit->payment_id = $response['id'] ?? null;
        $deposit->save();

        return $response;
    }
}
