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
        $this->webhookEnabled = config('waapi.webhook.enable', true);
        $this->autoRegister = config('waapi.webhook.auto_register', true);

        // تسجيل الـ route تلقائياً إذا كان مفعل
        if ($this->autoRegister && $this->webhookEnabled) {
            $this->registerWebhookRoute();
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

    /**
     * Process incoming webhook data from array
     */
    public function processWebhook(array $webhookData): array
    {
        try {
            // Log the incoming webhook
            Log::info('WhatsApp Webhook Received', $webhookData);

            // Extract data
            $sender = $webhookData['sender'] ?? null;
            $receiver = $webhookData['receiver'] ?? null;
            $conversation = $webhookData['payload']['conversation'] ?? null;
            $messageContextInfo = $webhookData['payload']['messageContextInfo'] ?? null;

            // Process the webhook data
            $processedData = [
                'sender' => $sender,
                'receiver' => $receiver,
                'conversation' => $conversation,
                'message_context' => $messageContextInfo,
                'processed_at' => now()->toDateTimeString()
            ];

            // Here you can add your business logic
            $this->handleWebhookBusinessLogic($processedData);

            Log::info('Webhook processed successfully', $processedData);

            return [
                'success' => true,
                'message' => 'Webhook processed successfully',
                'data' => $processedData
            ];

        } catch (\Exception $e) {
            Log::error('Webhook processing failed: ' . $e->getMessage(), [
                'exception' => $e,
                'webhook_data' => $webhookData
            ]);

            return [
                'success' => false,
                'message' => 'Webhook processing failed',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process webhook from Request object
     */
    public function processWebhookFromRequest(Request $request): array
    {
        return $this->processWebhook($request->all());
    }

    /**
     * Process webhook from JSON string
     */
    public function processWebhookFromJson(string $jsonData): array
    {
        $webhookData = json_decode($jsonData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'message' => 'Invalid JSON data',
                'error' => json_last_error_msg()
            ];
        }

        return $this->processWebhook($webhookData);
    }

    /**
     * Register webhook route automatically
     */
    protected function registerWebhookRoute(): void
    {
        if (!$this->webhookEnabled) {
            return;
        }

        Route::post($this->webhookUrl, function (Request $request) {
            return response()->json($this->processWebhookFromRequest($request));
        });
    }

    /**
     * Get full webhook URL for external services
     */
    public function getWebhookUrl(): string
    {
        return url($this->webhookUrl);
    }

    /**
     * Check if webhook is enabled
     */
    public function isWebhookEnabled(): bool
    {
        return $this->webhookEnabled;
    }

    /**
     * Get webhook configuration
     */
    public function getWebhookConfig(): array
    {
        return [
            'url' => $this->getWebhookUrl(),
            'enabled' => $this->webhookEnabled,
            'auto_register' => $this->autoRegister,
            'route' => $this->webhookUrl
        ];
    }

    /**
     * Business logic for handling webhook
     */
    protected function handleWebhookBusinessLogic(array $data): void
    {
        // Example business logic - you can customize this
        Log::info('Business logic executed for webhook', $data);

        // Here you can:
        // - Save to database
        // - Trigger events
        // - Send auto-reply
        // - etc.
    }
}