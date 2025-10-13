<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BillplzService
{
    /**
     * @param Order $order
     *
     * @return array
     */
    public function createOrderBillplzInvoice(Order $order): array
    {
        $referenceNumber = $order->code;
        $method          = $order->paymentMethod;

        $payload = [
            'collection_id'     => $method->slug,
            'description'       => $order->productItem->full_name,
            'email'             => $order->cust_email,
            'name'              => $order->cust_email,
            'amount'            => (int) $order->total_price * 1000,
            'callback_url'      => route('callback.billplz'),
            'redirect_url'      => config('app.fe_url').'/'.config('app.fe_invoice_url').'/'. $referenceNumber
        ];

        $response = $this->sendBillplzRequest($payload);

        Log::info('Billplz payment requests response', $response);

        $order->update([
            'payment_url'  => $response['url'] ?? null,
            'payment_code' => $response['id'] ?? null,
            'payment_id'   => $response['id'] ?? null,
        ]);

        return $response;
    }

    /**
     * @param Deposit $deposit
     *
     * @return array
     */
    public function createDepositBillplzInvoice(Deposit $deposit): array
    {
        $referenceNumber = $deposit->code;
        $method          = $deposit->paymentMethod;

        $payload = [
            'collection_id'     => $method->slug,
            'description'       => 'Deposit balance ' . $deposit->total_amount,
            'email'             => $deposit->user->email,
            'name'              => $deposit->user->name,
            'amount'            => (int) ceil($deposit->total_amount * 1000),
            'callback_url'      => route('callback.billplz'),
            'redirect_url'      => config('app.fe_url') . '/dashboard/deposit/' . $referenceNumber
        ];

        $response = $this->sendBillplzRequest($payload);

        Log::info('Billplz payment requests response', $response);

        $deposit->update([
            'payment_url'  => $response['url'] ?? null,
            'payment_code' => $response['id'] ?? null,
            'payment_id'   => $response['id'] ?? null,
        ]);

        return $response;
    }

    /**
     * @param array $payload
     *
     * @return array
     */
    private function sendBillplzRequest(array $payload): array
    {
        $response = Http::withBasicAuth(Setting::getByKey('billplz_api_key'), '')
            ->asForm()
            ->post(Setting::getByKey('billplz_api_url') . "/v3/bills", $payload);
        $json = $response->json();

        if ($response->failed()) {
            throw new \Exception("Failed to create payment: " . $json['error']['message'][0]);
        }

        return $json;
    }
}
