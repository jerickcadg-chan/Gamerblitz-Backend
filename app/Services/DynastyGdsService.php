<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class DynastyGdsService
{
    /**
     * @var string
     */
    protected string $url;

    /**
     * @var string
     */
    protected string $email;

    /**
     * @var string
     */
    protected string $password;

    /**
     * @var string|null
     */
    protected ?string $token;

    public function __construct()
    {
        $this->url      = Setting::getByKey('dynasty_gds_api_url');
        $this->email    = Setting::getByKey('dynasty_gds_email');
        $this->password = Setting::getByKey('dynasty_gds_password');

        $this->generateToken();
    }

    /**
     * Generate API token.
     */
    protected function generateToken(): void
    {
        $response = Http::retry(3, 200)
            ->timeout(5)
            ->post("{$this->url}/api/merchant/token", [
                'email'    => $this->email,
                'password' => $this->password,
            ])
            ->json();

        $this->token = $response['token'] ?? null;
    }

    /**
     * Create order request.
     *
     * @param  Order  $order
     * @return array
     */
    public function order(Order $order): array
    {
        $response = Http::retry(3, 200)
            ->timeout(5)
            ->post("{$this->url}/api/Order/Create", [
                'denomCode'   => $order->productItem->code,
                'inputs'      => json_decode($order->cust_account, true),
                'merchantRef' => $order->code,
            ]);

        return $response->json();
    }

    /**
     * Check order status.
     *
     * @param  string  $providerCode
     * @param  string  $orderCode
     * @return array
     */
    public function check(string $providerCode, string $orderCode): array
    {
        $response = Http::retry(3, 200)
            ->timeout(5)
            ->get("{$this->url}/api/Order/TrackOrder", [
                'orderNo'     => $providerCode,
                'merchantRef' => $orderCode,
            ]);

        return $response->json();
    }

    /**
     * Get product list.
     *
     * @return array
     */
    public function productList(): array
    {
        $response = Http::retry(3, 200)
            ->timeout(5)
            ->get("{$this->url}/api/Product/AvailableGameList");

        return $response->json();
    }

    /**
     * Get product detail.
     *
     * @param  string  $providerCode
     * @return array
     */
    public function productInfo(string $providerCode): array
    {
        $response = Http::retry(3, 200)
            ->timeout(5)
            ->get("{$this->url}/api/Product/GameInfo/{$providerCode}");

        return $response->json();
    }
}