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

class XenditV2Service
{
    /**
     * @throws ConnectionException
     */
    public function createOrderXenditInvoice(Order $order): array
    {
        $amount = $order->total_price;
        $externalId = $order->code;
        $method = $order->paymentMethod;

        $payload = [
            "external_id" => $externalId,
            "amount" => $amount,
            "description" => $order->productItem->full_name,
            "invoice_duration" => 86400,
            "customer" => [
                "given_names" => $order->cust_email,
                "surname" => $order->cust_email,
                "email" => $order->cust_email,
                "mobile_number" => $order->cust_phone_number,
            ],
            "success_redirect_url" => config('app.fe_url') . '/' . config('app.fe_invoice_url') . '/' . $externalId,
            "failure_redirect_url" => config('app.fe_url') . '/' . config('app.fe_invoice_url') . '/' . $externalId,
            "currency" => $method->currency_code,
            "payment_methods" => ["CREDIT_CARD"],
            "metadata" => [
                'type' => 'order',
                'order_code' => $externalId,
            ],
        ];

        $response = Http::withBasicAuth(Setting::getByKey('xendit_secret_key'), '')
            ->withHeaders(['api-version' => '2024-11-11', 'Content-Type' => 'application/json'])
            ->post(Setting::getByKey('xendit_api_url') . '/v2/invoices', $payload);

        $r = $response->json();

        Log::info('Xendit payment requests response', $r);

        if ($response->failed()) {
            throw new \Exception("Failed to create payment: " . $r['message']);
        }

        $paymentUrl = $r['invoice_url'];
        $paymentCode = $r['id'];

        $order->payment_url = $paymentUrl;
        $order->payment_code = $paymentCode;
        $order->payment_id = $paymentCode;
        $order->save();

        return $r;
    }

    public function createDepositXenditInvoice(Deposit $deposit): array
    {
        $amount = ceil($deposit->total_amount);
        $externalId = $deposit->code;
        $method = $deposit->paymentMethod;

        $payload = [
            "external_id" => $externalId,
            "amount" => $amount,
            "invoice_duration" => 86400,
            "customer" => [
                "given_names" => $deposit->user->name,
                "surname" => $deposit->user->name,
                "email" => $deposit->user->email,
                "mobile_number" => $deposit->user->phone_number,
            ],
            "success_redirect_url" => config('app.fe_url') . '/dashboard/deposit/' . $externalId,
            "failure_redirect_url" => config('app.fe_url') . '/dashboard/deposit/' . $externalId,
            "currency" => $method->currency_code,
            "payment_methods" => ["CREDIT_CARD"],
            "metadata" => [
                'type' => 'deposit',
                'deposit_code' => $externalId,
            ],
        ];

        $response = Http::withBasicAuth(Setting::getByKey('xendit_secret_key'), '')
            ->withHeaders(['api-version' => '2024-11-11', 'Content-Type' => 'application/json'])
            ->post(Setting::getByKey('xendit_api_url') . '/v2/invoices', $payload);

        $json = $response->json();

        Log::info('Xendit payment requests response', $json);

        if ($response->failed()) {
            throw new \Exception("Failed to create payment: " . $json['message']);
        }

        $paymentUrl = $json['invoice_url'];
        $paymentCode = $json['id'];
        
        $deposit->payment_url = $paymentUrl;
        $deposit->payment_code = $paymentCode;
        $deposit->payment_id = $paymentCode;
        $deposit->save();

        return $json;
    }
}
