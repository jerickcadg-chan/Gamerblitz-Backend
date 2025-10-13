<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpayService
{
    /**
     * @param Order $order
     *
     * @return array
     */
    public function createOrderMpayInvoice(Order $order): array
    {
        $amount = $order->total_price;
        $externalId = $order->code;
        $method = $order->paymentMethod;

        $payload = [
            'amount'            => $amount,
            'notifyUrl'         => 'https://webhook.site/1bead8cb-0577-42a8-ace8-e90b0d75807c',
            'customerPhone'     => $order->cust_phone_number,
            'customerName'      => $order->cust_email,
            'customerEmail'     => $order->cust_email,
            'merchantOrderId'   => $externalId,
            'payMethod'         => $method->slug
        ];

        $response = Http::withBasicAuth(
            Setting::getByKey('mpay_app_id'),
            Setting::getByKey('mpay_sign_key')
        )->post(Setting::getByKey('mpay_api_url') . "/open-api/pay/payment", $payload);

        $json = $response->json();
        Log::info('Mpay payment requests response', $json);

        if ($response->failed()) {
            throw new \Exception("Failed to create payment: " . $json['message']);
        }

        dd($payload, $json);

        $order->payment_url = $json['payUrl'] ?? null;
        $order->payment_code = $json['orderId'] ?? null;
        $order->payment_id = $json['orderId'] ?? null;
        $order->save();

        return $json;
    }

    /**
     * @param Deposit $deposit
     *
     * @return array
     */
    public function createDepositMpayInvoice(Deposit $deposit): array
    {
        $amount = ceil($deposit->total_amount);
        $externalId = $deposit->code;
        $method = $deposit->paymentMethod;

        $payload = [
            'amount'            => $amount,
            'notifyUrl'         => route('callback.mpay'),
            'customerPhone'     => $deposit->user->phone_number,
            'customerName'      => $deposit->user->name,
            'customerEmail'     => $deposit->user->email,
            'merchantOrderId'   => $externalId,
            'payMethod'         => $method->slug
        ];

        $response = Http::withBasicAuth(
            Setting::getByKey('mpay_app_id'),
            Setting::getByKey('mpay_sign_key')
        )->post(Setting::getByKey('mpay_api_url') . "/open-api/pay/payment", $payload);

        $json = $response->json();
        Log::info('Mpay payment requests response', $json);

        if ($response->failed()) {
            throw new \Exception("Failed to create payment: " . $json['message']);
        }

        $deposit->payment_url = $json['payUrl'] ?? null;
        $deposit->payment_code = $json['orderId'] ?? null;
        $deposit->payment_id = $json['orderId'] ?? null;
        $deposit->save();

        return $json;
    }
}
