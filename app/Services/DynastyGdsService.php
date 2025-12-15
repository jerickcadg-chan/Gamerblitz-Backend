<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
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
     * 
     * @return void
     */
    protected function generateToken(): void
    {
        $response = $this->safeRequest(
            "{$this->url}/api/Merchant/Token",
            "POST",
            [
                'email'    => $this->email,
                'password' => $this->password,
            ],
            false
        );

        $this->token = $response['token'] ?? null;
    }

    /**
     * Create order request.
     * 
     * @param Order $order
     * 
     * @return array|null
     */
    public function order(Order $order): ?array
    {
        return $this->safeRequest(
            "{$this->url}/api/Order/Create",
            "POST",
            [
                'denomCode'   => $order->productItem->code,
                'inputs'      => json_decode($order->cust_account, true),
                'merchantRef' => $order->code,
            ]
        );
    }

    /**
     * Check order status.
     * 
     * @param string $providerCode
     * @param string $orderCode
     * 
     * @return array|null
     */
    public function check(string $providerCode, string $orderCode): ?array
    {
        return $this->safeRequest(
            "{$this->url}/api/Order/TrackOrder",
            "GET",
            [
                'orderNo'     => $providerCode,
                'merchantRef' => $orderCode,
            ]
        );
    }

    /**
     * Get product list.
     * 
     * @return array|null
     */
    public function productList(): ?array
    {
        return $this->safeRequest(
            "{$this->url}/api/Product/AvailableGameList",
            "GET"
        );
    }

    /**
     * Get product detail.
     * 
     * @param string $providerCode
     * 
     * @return array|null
     */
    public function productInfo(string $providerCode): ?array
    {
        return $this->safeRequest(
            "{$this->url}/api/Product/GameInfo/{$providerCode}",
            "GET"
        );
    }

    /**
     * @param string $url
     * @param string $method
     * @param array|null $payload
     * @param bool $useToken
     * 
     * @return array|null
     */
    public function safeRequest(string $url, string $method, ?array $payload = null, bool $useToken = true): ?array {
        try {
            $request = Http::retry(3, 200)->timeout(5);
    
            if ($useToken && $this->token) {
                $request = $request->withToken($this->token);
            }
    
            $response = $method === 'GET'
                ? $request->get($url, $payload)
                : $request->post($url, $payload);
    
            return $response->json();
        } catch (ConnectionException $e) {
            return [
                'statusCode' => 408,
                'statusMessage' => 'timeout',
                'errorMessage' => $e->getMessage(),
            ];
        } catch (RequestException $e) {
            return $e->response->json();
        }
    }
    
}
