<?php

namespace App\Services;

use App\Constants\CurrencyConstant;
use App\Data\Xendit\PaymentRequestResponse;
use App\Data\Xendit\PaymentRequestPayload;
use App\Models\Deposit;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Setting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditService
{
    /**
     * @throws ConnectionException
     */
    public function createOrderXenditInvoice(Order $order): array
    {
        $amount = ceil($order->total_price);
        $externalId = $order->code;
        $expiresAt = now()->addHour(1)->toIso8601String();

        $method = PaymentMethod::where('name', $order->payment_method)->firstOrFail();

        $country = app(CurrencyConstant::class)->metadata($method->currency_code);
        $countryLocale = explode('-', $country['locale'])[1];

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

        $order->payment_url = $paymentUrl;
        $order->payment_code = $paymentCode;
        $order->payment_id = $r['id'] ?? $r['payment_request_id'] ?? null;
        $order->save();

        return $r;
    }

    public function createDepositXenditInvoice(Deposit $deposit): array
    {
        $amount = ceil($deposit->total_amount);
        $externalId = $deposit->code;
        $expiresAt = now()->addHour(1)->toIso8601String();

        $method = $deposit->paymentMethod;

        $currencyCode = $method->currency_code;
        $countryCode = CurrencyConstant::countryCodeByCurrency($method->currency_code);
        $channelCode = $method->slug;

        $returnUrl = config('app.fe_url').'/dashboard/deposits/' . $externalId;

        $payload = PaymentRequestPayload::from([
            'reference_id'   => $externalId,
            'type'           => 'PAY',
            'country'        => $countryCode,
            'currency'       => $currencyCode,
            'request_amount' => (float) $amount,
            'capture_method' => 'AUTOMATIC',
            'channel_code'   => $channelCode,
            'channel_properties' => [
                'expires_at' => $expiresAt,
                'success_return_url' => $returnUrl,
                'failure_return_url' => $returnUrl,
                'cancel_return_url' => $returnUrl,
            ],
            'description' => "Deposit {$deposit->amount}",
            'metadata'    => [
                'deposit_code' => $externalId,
            ],
        ]);

        $r = Http::withBasicAuth(Setting::getByKey('xendit_secret_key'), '')
            ->withHeaders(['api-version' => '2024-11-11', 'Content-Type' => 'application/json'])
            ->post(Setting::getByKey('xendit_api_url').'/v3/payment_requests', $payload->toArray())
            ->json();

        Log::info('Xendit payment requests response', $r);

        $response = PaymentRequestResponse::from($r);

        $paymentUrl = null;
        $paymentCode = null;

        if ($response->actions[0]->type === "REDIRECT_CUSTOMER") {
            $paymentUrl = $response->actions[0]->value;
        }

        if ($response->actions[0]->type === "PRESENT_TO_CUSTOMER") {
            $paymentCode = $response->actions[0]->value;
        }

        $deposit->payment_url = $paymentUrl;
        $deposit->payment_code = $paymentCode;
        $deposit->payment_id = $response->payment_request_id ?? null;
        $deposit->save();

        return $r;

    }
}
