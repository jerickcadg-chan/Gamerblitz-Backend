<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookCryptomus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cryptomus:test-webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test webhook payload to Cryptomus API for debugging or verification.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $apiKey = Setting::where('key', 'cryptomus_api_key')->value('value');
        $merchantId = Setting::getByKey('cryptomus_merchant_id');
        $apiUrl = Setting::getByKey('cryptomus_api_url');

        if (!$apiKey || !$merchantId || !$apiUrl) {
            $this->error('❌ Missing Cryptomus configuration in settings table.');
            return;
        }

        $uuid = $this->ask('Enter the transaction UUID');
        $callbackUrl = $this->ask('Enter the callback URL (e.g. https://yourdomain.com/api/callback/cryptomus)');

        $payload = [
            "uuid" => $uuid,
            "currency" => "USDT",
            "url_callback" => $callbackUrl,
            "network" => "tron",
            "status" => "paid",
        ];

        $signature = md5(base64_encode(json_encode($payload)) . $apiKey);

        $this->info("🚀 Sending webhook to: {$apiUrl}");

        $response = Http::withHeaders([
            'merchant' => $merchantId,
            'sign' => $signature,
        ])->post("{$apiUrl}/test-webhook/payment", $payload);

        if ($response->successful()) {
            $this->info('✅ Webhook sent successfully!');
        } else {
            $this->error('❌ Failed to send webhook: ' . $response->status());
        }

        Log::info('Webhook Cryptomus test result', [
            'payload' => $payload,
            'response' => $response->json(),
        ]);
    }
}
