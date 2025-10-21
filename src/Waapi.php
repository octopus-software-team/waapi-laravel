<?php

namespace OctopusTeam\Waapi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class Waapi
{
    protected string $url;
    protected string $appkey;
    protected string $authkey;
    protected string $webhookUrl;
    protected bool $webhookEnabled;
    protected bool $autoRegister;

    public function __construct()
    {
        $this->url = config('waapi.app_url');
        $this->appkey = config('waapi.app_key');
        $this->authkey = config('waapi.auth_key');
        $this->webhookUrl = config('waapi.webhook.url', '/api/webhook/whatsapp');
        $this->webhookEnabled = config('waapi.webhook.enabled', true);
        $this->autoRegister = config('waapi.webhook.auto_register', true);

        // تسجيل الـ route تلقائياً إذا كان مفعل
        if ($this->autoRegister && $this->webhookEnabled) {
            $this->registerWebhookRoute();
        }
    }

    /**
     * Register webhook route automatically.
     */
    protected function registerWebhookRoute(): void
    {
        Route::post($this->webhookUrl, function (Request $request) {
            return $this->handleWebhook($request);
        })->name('waapi.webhook');
    }

    /**
     * Handle incoming webhook data.
     */
    public function handleWebhook(Request $request): array
    {
        try {
            $data = $request->all();

            // Log the incoming webhook data
            Log::info('WAAPI Webhook received:', $data);

            // يمكنك معالجة البيانات هنا
            // مثل: fire event, store in database, etc.

            return [
                'success' => true,
                'message' => 'Webhook received successfully',
                'data' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('WAAPI Webhook error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get webhook data from Webhook.site API.
     */
    public function getWebhookSiteData(int $limit = 50): array
    {
        $token = config('waapi.webhook.webhook_site_token');

        if (!$token) {
            return [
                'success' => false,
                'error' => 'Webhook.site token not configured',
            ];
        }

        try {
            $url = "https://webhook.site/token/{$token}/requests";

            $request = Http::withOptions(['verify' => false]);

            $response = $request->get($url, ['per_page' => $limit]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'status' => $response->status(),
                'error' => $response->body(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get specific webhook request from Webhook.site.
     */
    public function getWebhookSiteRequest(string $requestId): array
    {
        $token = config('waapi.webhook.webhook_site_token');

        if (!$token) {
            return [
                'success' => false,
                'error' => 'Webhook.site token not configured',
            ];
        }

        try {
            $url = "https://webhook.site/token/{$token}/request/{$requestId}";

            $request = Http::withOptions(['verify' => false]);

            $response = $request->get($url);

            return $this->formatResponse($response);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Standardized response formatter.
     */
    protected function formatResponse($httpResponse): array
    {
        if ($httpResponse->successful()) {
            return [
                'success' => true,
                'status' => $httpResponse->status(),
                'data' => $httpResponse->json(),
            ];
        }

        return [
            'success' => false,
            'status' => $httpResponse->status(),
            'error' => $httpResponse->body(),
        ];
    }

    /**
     * Send a message to a single phone.
     */
    public function sendMessage(string $phone, string $message, bool $verify = false, bool $sandbox = false): array
    {
        $response = Http::withOptions(['verify' => $verify])
            ->asForm()
            ->post($this->url, [
                'appkey' => $this->appkey,
                'authkey' => $this->authkey,
                'to' => $phone,
                'message' => $message,
                'sandbox' => $sandbox,
            ]);

        return $this->formatResponse($response);
    }

    /**
     * Generate OTP.
     */
    public function generateOtp($length = 6): int
    {
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= mt_rand(0, 9);
        }
        return (int) $otp;
    }

    /**
     * Send an OTP message.
     */
    public function sendOtp(string $phone, string $otp, bool $verify = false, bool $sandbox = false): array
    {
        $response = Http::withOptions(['verify' => $verify])
            ->asForm()
            ->post($this->url, [
                'appkey' => $this->appkey,
                'authkey' => $this->authkey,
                'to' => $phone,
                'message' => "{$otp} is your code. Don't share it.",
                'sandbox' => $sandbox,
            ]);

        return $this->formatResponse($response);
    }
}
