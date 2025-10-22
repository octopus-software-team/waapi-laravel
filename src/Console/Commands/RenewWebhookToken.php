<?php

namespace OctopusTeam\Waapi\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RenewWebhookToken extends Command
{
    protected $signature = 'waapi:webhook-renew';
    protected $description = 'Renew webhook.site token';

    public function handle()
    {
        try {
            $newToken = $this->createNewWebhookToken();

            if (!$newToken) {
                $this->error('Failed to create new webhook token');
                Log::error('Failed to create new webhook token');
                return 1;
            }

            $this->info("New webhook token created: {$newToken['uuid']}");
            Log::info("New webhook token created: {$newToken['uuid']}");

            $this->updateEnvFile($newToken['uuid']);

            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Webhook renewal error: ' . $e->getMessage());
            return 1;
        }
    }

    private function createNewWebhookToken(): ?array
    {
        try {
            $response = Http::withOptions(['verify' => false])
                ->post('https://webhook.site/token');

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('Failed to create webhook token: ' . $e->getMessage());
            return null;
        }
    }

    private function updateEnvFile(string $token): void
    {
        $envFile = base_path('.env');
        if (!file_exists($envFile)) {
            return;
        }

        $content = file_get_contents($envFile);

        if (preg_match('/WAAPI_WEBHOOK_SITE_TOKEN=(.*)/', $content)) {
            $content = preg_replace(
                '/WAAPI_WEBHOOK_SITE_TOKEN=(.*)/',
                "WAAPI_WEBHOOK_SITE_TOKEN={$token}",
                $content
            );
        } else {
            $content .= "\nWAAPI_WEBHOOK_SITE_TOKEN={$token}";
        }

        file_put_contents($envFile, $content);

        // Update the webhook URL via API
        Http::withOptions(['verify' => false])
            ->get('https://waapi.octopusteam.net/update-hook-by-uuid', [
                'uuid' => env('WAAPI_UPDATE_DEVICE_WEBHOOK'),
                'hook_url' => "https://webhook.site/{$token}",
            ]);

        $this->info('.env file updated with new token');
        $this->info('Webhook URL updated via API');
    }
}