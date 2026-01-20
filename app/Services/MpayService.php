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
            'amount'            => (int) $amount * 100,
            'notifyUrl'         => route('callback.mpay'),
            'customerPhone'     => $this->normalizePhone($order->cust_phone_number),
            'customerName'      => $this->emailToName($order->cust_email),
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

        if ($response->failed() || $json['code'] != '200') {
            throw new \Exception("Failed to create payment: " . $json['msg']);
        }

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
        $amount = ceil($deposit->total_amount * 100);
        $externalId = $deposit->code;
        $method = $deposit->paymentMethod;

        $payload = [
            'amount'            => (int) $amount,
            'notifyUrl'         => route('callback.mpay'),
            'customerPhone'     => $this->normalizePhone($deposit->user->phone_number),
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

        if ($response->failed() || $json['code'] != '200') {
            throw new \Exception("Failed to create payment: " . $json['msg']);
        }

        $deposit->payment_url = $json['payUrl'] ?? null;
        $deposit->payment_code = $json['orderId'] ?? null;
        $deposit->payment_id = $json['orderId'] ?? null;
        $deposit->save();

        return $json;
    }

    /**
     * @param mixed $email
     *
     * @return string
     */
    private function emailToName($email): string
    {
        $username = explode('@', $email)[0];
        $username = str_replace(['.', '_'], ' ', $username);

        return ucwords($username);
    }

    /**
     * @param mixed $phone
     *
     * @return string
     */
    public function normalizePhone(?string $phone): ?string
    {
        if (!$phone) {
            return '08' . str_pad(rand(0, 999999999), 9, '0', STR_PAD_LEFT);
        }

        // Remove all non-digit characters
        $onlyDigits = preg_replace('/\D/', '', $phone);

        // Convert prefix "62" to "0"
        if (str_starts_with($onlyDigits, '62')) {
            $onlyDigits = '0' . substr($onlyDigits, 2);
        }

        // If the number doesn't start with "0", prepend "0"
        if (!str_starts_with($onlyDigits, '0')) {
            $onlyDigits = '0' . $onlyDigits;
        }

        // If the number is shorter than 11 digits, pad with trailing zeros
        if (strlen($onlyDigits) < 11) {
            $onlyDigits = str_pad($onlyDigits, 11, '0');
        }

        // If the number is longer than 11 digits, truncate it
        $normalized = substr($onlyDigits, 0, 11);

        return $normalized;
    }
}
