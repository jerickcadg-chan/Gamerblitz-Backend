<?php

namespace App\Services;

use App\Constants\CurrencyConstant;
use App\Models\PaymentMethod;
use App\Models\Setting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class XenditService
{
    /**
     * @throws ConnectionException
     */
    public function createXenditInvoice($order): array
    {
        $amount = ceil($order->total_price);
        $externalId = $order->code;
        $expiresAt = now()->addHour(1)->toIso8601String();

        $method = PaymentMethod::where('name', $order->payment_method)->firstOrFail();

        $country = app(CurrencyConstant::class)->metadata($method->currency_code);
        $countryLocale = explode('-', $country['locale'])[1];

        return $this->createMainInvoice($order, $method, $countryLocale, $amount, $externalId, $expiresAt);
    }

    /**
     * @throws ConnectionException
     */
    public function createMainInvoice($order, $method, $countryLocale, $amount, $externalId, $expiresAt): array
    {
        $payload = [
            'reference_id'   => $externalId,
            'type'           => 'PAY',
            'country'        => $countryLocale,
            'currency'       => $method->currency_code,
            'request_amount' => (int) $amount,
            'capture_method' => 'AUTOMATIC',
            'channel_code'   => $method->slug,
            'channel_properties' => [
                'expires_at' => $expiresAt,
                'success_return_url' => config('app.fe_url').'/payment/'.$externalId,
                'failure_return_url' => config('app.fe_url').'/payment/'.$externalId,
                'cancel_return_url' => config('app.fe_url').'/payment/'.$externalId,
            ],
            'description' => "{$order->productItem->product->name} {$order->productItem->name}",
            'metadata'    => [
                'order_code' => $externalId,
            ],
        ];

        $r = Http::withBasicAuth(Setting::getByKey('xendit_secret_key'), '')
            ->withHeaders(['api-version' => '2024-11-11', 'Content-Type' => 'application/json'])
            ->post(Setting::getByKey('xendit_api_url').'/v3/payment_requests', $payload)
            ->json();

        $paymentUrl = null;
        $paymentCode = null;

        if ($r['actions'][0]['type'] === "REDIRECT_CUSTOMER") {
            $paymentUrl = $r['actions'][0]['value'];
        }

        if ($r['actions'][0]['type'] === "PRESENT_TO_CUSTOMER") {
            $paymentCode = $r['actions'][0]['value'];
        }

        return $this->saveXenditResponse($order, $r, $paymentUrl, $paymentCode);
    }

    public function saveXenditResponse($order, array $r, $paymentUrl = null, $qrCode = null): array
    {
        $order->payment_url = $paymentUrl;
        $order->payment_code = $qrCode;
        $order->payment_id = $r['id'] ?? $r['payment_request_id'] ?? null;
        $order->save();

        return $r;
    }
}
