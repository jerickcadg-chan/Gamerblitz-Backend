<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class CryptomusService
{
    /**
     * @param Order $order
     * 
     * @return array
     */
    public function createOrderCryptomusInvoice(Order $order): array
    {
        $apiKey = Setting::where('key', 'cryptomus_api_key')->first()?->value ?? null;
        $merchantId = Setting::getByKey('cryptomus_merchant_id');

        $payload = [
            'amount'       => (string) ceil($order->total_price),
            'currency'     => $order->paymentMethod->currency_code,
            'order_id'     => $order->code,
            'url_return'   => $order->payment_url_full,
            'url_success'  => $order->payment_url_full,
            'url_callback' => route('callback.cryptomus'),
        ];

        $response = Http::withHeaders([
            'merchant' => $merchantId,
            'sign'     => md5(
                base64_encode(json_encode($payload)) .
                $apiKey
            ),
        ])->post(
            Setting::getByKey('cryptomus_api_url') . '/payment',
            $payload
        );

        $json = $response->json();

        Log::info('Cryptomus payment request response', $json);

        if ($response->failed()) {
            throw new Exception('Failed to create payment: ' . ($json['message'] ?? 'Unknown error'));
        }

        $order->update([
            'payment_url'  => $json['result']['url'] ?? null,
            'payment_code' => $json['result']['order_id'] ?? null,
            'payment_id'   => $json['result']['uuid'] ?? null,
        ]);

        return $json;
    }
    
    /**
     * @param Deposit $deposit
     * 
     * @return array
     */
    public function createDepositCryptomusInvoice(Deposit $deposit): array
    {
        $apiKey = Setting::where('key', 'cryptomus_api_key')->first()?->value ?? null;
        $merchantId = Setting::getByKey('cryptomus_merchant_id');

        $payload = [
            'amount'       => (string) ceil($deposit->total_amount),
            'currency'     => $deposit->paymentMethod->currency_code,
            'order_id'     => $deposit->code,
            'url_return'   => config('app.fe_url') . '/dashboard/deposit/' . $deposit->code,
            'url_success'  => config('app.fe_url') . '/dashboard/deposit/' . $deposit->code,
            'url_callback' => route('callback.cryptomus'),
        ];

        $response = Http::withHeaders([
            'merchant' => $merchantId,
            'sign'     => md5(
                base64_encode(json_encode($payload)) .
                $apiKey
            ),
        ])->post(
            Setting::getByKey('cryptomus_api_url') . '/payment',
            $payload
        );

        $json = $response->json();

        Log::info('Cryptomus deposit request response', $json);

        if ($response->failed()) {
            throw new Exception('Failed to create payment: ' . ($json['message'] ?? 'Unknown error'));
        }

        $deposit->update([
            'payment_url'  => $json['result']['url'] ?? null,
            'payment_code' => $json['result']['order_id'] ?? null,
            'payment_id'   => $json['result']['uuid'] ?? null,
        ]);

        return $json;
    }
}