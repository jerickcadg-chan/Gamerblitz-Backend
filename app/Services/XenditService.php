<?php

namespace App\Services;

use App\Constants\CurrencyConstant;
use App\Data\Xendit\PaymentRequestResponse;
use App\Data\Xendit\PaymentRequestPayload;
use App\Models\Deposit;
use App\Models\Order;
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
        $amount = $order->total_price;
        $externalId = $order->code;
        $expiresAt = now()->addHour(1)->toIso8601String();

        $method = $order->paymentMethod;

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
                'payer_name' => $order->user?->name ?? "guest user",
                'success_return_url' => config('app.fe_url') . '/' . config('app.fe_invoice_url') . '/' . $externalId,
                'failure_return_url' => config('app.fe_url') . '/' . config('app.fe_invoice_url') . '/' . $externalId,
                'cancel_return_url' => config('app.fe_url') . '/' . config('app.fe_invoice_url') . '/' . $externalId,
                'card_details' => [
                    'cvn' => $order->additional_informations['cvc'] ?? null,
                    'card_number' => $order->additional_informations['card_number'] ?? null,
                    'expiry_year' => $order->additional_informations['expiry_year'] ?? null,
                    'expiry_month' => $order->additional_informations['expiry_month'] ?? null,
                    'cardholder_first_name' => $order->additional_informations['first_name'] ?? null,
                    'cardholder_last_name' => $order->additional_informations['last_name'] ?? null,
                    'cardholder_email' => $order->additional_informations['holder_email'] ?? null,
                    'cardholder_phone_number' => '+' . $order->additional_informations['holder_phone_number'] ?? null
                ]
            ],
            'description' => "{$order->productItem->product->name} {$order->productItem->name}",
            'metadata'    => [
                'type' => 'order',
                'order_code' => $externalId,
            ],
            'customer' => [
                'reference_id'  => $externalId,
                'type'          => 'INDIVIDUAL',
                'individual_detail' => [
                    'given_names'   => $order->user?->name ?? "guest user",
                    'email'         => $order->cust_email ?? "",
                    'mobile_number' => $order->cust_phone_number ?? "",
                ]
            ]
        ];

        $response = Http::withBasicAuth(Setting::getByKey('xendit_secret_key'), '')
            ->withHeaders(['api-version' => '2024-11-11', 'Content-Type' => 'application/json'])
            ->post(Setting::getByKey('xendit_api_url') . '/v3/payment_requests', $payload);

        $r = $response->json();

        Log::info('Xendit payment requests response', $r);

        if ($response->failed()) {
            throw new \Exception("Failed to create payment: " . $r['message']);
        }

        $paymentUrl = null;
        $paymentCode = null;

        if ($r['actions'][0]['type'] === "REDIRECT_CUSTOMER") {
            $paymentUrl = $r['actions'][0]['value'];
        }

        if ($r['actions'][0]['type'] === "PRESENT_TO_CUSTOMER") {
            $paymentCode = $r['actions'][0]['value'];
        }

        $order->payment_descriptor = $r['actions'][0]['descriptor'];
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

        $returnUrl = config('app.fe_url') . '/dashboard/deposit/' . $externalId;

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
                'card_details' => [
                    'cvn' => $deposit->additional_informations['cvc'] ?? null,
                    'card_number' => $deposit->additional_informations['card_number'] ?? null,
                    'expiry_year' => $deposit->additional_informations['expiry_year'] ?? null,
                    'expiry_month' => $deposit->additional_informations['expiry_month'] ?? null,
                    'cardholder_first_name' => $deposit->additional_informations['first_name'] ?? null,
                    'cardholder_last_name' => $deposit->additional_informations['last_name'] ?? null,
                    'cardholder_email' => $deposit->additional_informations['holder_email'] ?? null,
                    'cardholder_phone_number' => '+' . $deposit->additional_informations['holder_phone_number'] ?? null
                ]
            ],
            'description' => "Deposit {$deposit->amount}",
            'metadata'    => [
                'type' => 'deposit',
                'deposit_code' => $externalId,
            ],
            'customer' => [
                'reference_id'  => $externalId,
                'type'          => 'INDIVIDUAL',
                'individual_detail' => [
                    'given_names'   => $deposit->user?->name ?? "guest user",
                    'email'         => $deposit->user?->email ?? "",
                    'mobile_number' => $deposit->user?->phone_number ?? "",
                ]
            ]
        ]);

        $r = Http::withBasicAuth(Setting::getByKey('xendit_secret_key'), '')
            ->withHeaders(['api-version' => '2024-11-11', 'Content-Type' => 'application/json'])
            ->post(Setting::getByKey('xendit_api_url') . '/v3/payment_requests', $payload->toArray());

        $json = $r->json();

        Log::info('Xendit payment requests response', $json);

        if ($r->failed()) {
            throw new \Exception("Failed to create payment: " . $json['message']);
        }

        $response = PaymentRequestResponse::from($json);

        $paymentUrl = null;
        $paymentCode = null;

        if ($response->actions[0]->type === "REDIRECT_CUSTOMER") {
            $paymentUrl = $response->actions[0]->value;
        }

        if ($response->actions[0]->type === "PRESENT_TO_CUSTOMER") {
            $paymentCode = $response->actions[0]->value;
        }

        $deposit->payment_descriptor = $response->actions[0]->descriptor;
        $deposit->payment_url = $paymentUrl;
        $deposit->payment_code = $paymentCode;
        $deposit->payment_id = $response->payment_request_id ?? null;
        $deposit->save();

        return $json;
    }
}
