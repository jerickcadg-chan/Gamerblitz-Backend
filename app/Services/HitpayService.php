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
            ->post(Setting::getByKey('hitpay_api_url') . "/payment-requests", $payload);

        $json = $response->json();
        Log::info('Hitpay payment requests response', $json);

        if ($response->failed()) {
            throw new \Exception("Failed to create payment: " . $json['message']);
        }

        $order->payment_url = $json['url'] ?? null;
        $order->payment_code = $json['reference_number'] ?? null;
        $order->payment_id = $json['id'] ?? null;
        $order->save();

        return $json;
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
            'redirect_url'     => config('app.fe_url') . '/dashboard/deposit/' . $externalId,
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
            ->post(Setting::getByKey('hitpay_api_url') . "/payment-requests", $payload);

        $json = $response->json();
        Log::info('Hitpay payment requests response', $json);

        if ($response->failed()) {
            throw new \Exception("Failed to create payment: " . $json['message']);
        }

        $deposit->payment_url = $json['url'] ?? null;
        $deposit->payment_code = $json['reference_number'] ?? null;
        $deposit->payment_id = $json['id'] ?? null;
        $deposit->save();

        return $json;
    }
}
