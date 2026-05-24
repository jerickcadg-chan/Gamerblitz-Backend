<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class LapakGamingApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:lapak-gaming-api {--path=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used to fetch lapak gaming api, for debugging or just data retrieval';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $token = Setting::getByKey(Setting::KEY_LAPAKGAMING_API_TOKEN);
        $baseUrl = Setting::getByKey(Setting::KEY_LAPAKGAMING_API_URL);

        if (!$token) {
            throw new \Exception('Missing LapakGaming api token in setting');
        }

        if (!$baseUrl) {
            throw new \Exception('Missing LapakGaming api url in setting');
        }


        $path = $this->option('path');

        if ($path) {
            $response = Http::withToken($token)->get($baseUrl . $path);
            if ($response->failed()) {
                $this->error('LapakGaming: fetch failed');
                $this->line('Status: '.$response->status());
                $this->line('Body: '.$response->body());
                return;
            }

            $this->info(json_encode($response->json(), JSON_PRETTY_PRINT));
        }
    }
}
