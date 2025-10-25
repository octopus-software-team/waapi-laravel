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

    /**
     * Waapi constructor.
     *
     * Initializes the Waapi client with configuration values.
     * It sets up the API credentials and webhook settings.
     * If auto-registration for webhooks is enabled, it registers the webhook route.
     */
    public function __construct()
    {
        $this->url = config('waapi.app_url');
        $this->appkey = config('waapi.app_key');
        $this->authkey = config('waapi.auth_key');
        $this->webhookUrl = config('waapi.webhook.url', '/api/webhook/whatsapp');
        $this->webhookEnabled = config('waapi.webhook.enabled', true);
        $this->autoRegister = config('waapi.webhook.auto_register', true);

        // Register the route automatically if enabled
        if ($this->autoRegister && $this->webhookEnabled) {
            $this->registerWebhookRoute();
        }
    }

    /**
     * Register the webhook route automatically.
     *
     * This function creates a POST route that listens for incoming webhook
     * notifications from the Waapi service.
     *
     * @return void
     */
    protected function registerWebhookRoute(): void
    {
        Route::post($this->webhookUrl, function (Request $request) {
            return $this->handleWebhook($request);
        })->name('waapi.webhook');
    }

    /**
     * Handle incoming webhook data.
     *
     * This function processes the data sent from the Waapi webhook.
     * It logs the incoming data and returns a success response.
     * You can customize this function to process the data as needed (e.g., fire an event, store in the database).
     *
     * @param Request $request The incoming HTTP request.
     * @return array A response array indicating success or failure.
     */
    public function handleWebhook(Request $request): array
    {
        try {
            $data = $request->all();

            // Log the incoming webhook data
            Log::info('WAAPI Webhook received:', $data);

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
     * Get webhook data from the Webhook.site API.
     *
     * Fetches the latest requests sent to your Webhook.site URL.
     *
     * @param int $limit The maximum number of requests to retrieve.
     * @return array The API response, containing data or an error message.
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
     * Get and decode the content of webhook requests from Webhook.site.
     *
     * This function retrieves webhook data and extracts the 'content' field from each request,
     * decoding it from JSON if possible.
     *
     * @param int $limit The maximum number of requests to retrieve.
     * @return array An array of the decoded content from each request.
     */
    public function getWebHookSiteContent(int $limit = 50)
    {
        $data = $this->getWebhookSiteData($limit);

        if ($data['success']) {
            // Check for data existence
            if (isset($data['data']['data']) && is_array($data['data']['data']) && count($data['data']['data']) > 0) {
                $allRequests = [];

                // Loop through each request
                foreach ($data['data']['data'] as $request) {
                    // Check for content
                    if (isset($request['content'])) {
                        // Try to decode JSON
                        $content = json_decode($request['content'], true);

                        // Check if decode was successful
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $allRequests[] = $content;
                        } else {
                            // If not JSON, return the content as is
                            $allRequests[] = $request['content'];
                        }
                    }
                }

                return $allRequests;
            }

            return []; // No data
        }

        return []; // Request failed
    }
    /**
     * Get a specific webhook request from Webhook.site by its ID.
     *
     * @param string $requestId The unique ID of the request to retrieve.
     * @return array The API response for the specific request.
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
     * Standardize the format of the HTTP response.
     *
     * @param \Illuminate\Http\Client\Response $httpResponse The HTTP response object.
     * @return array A formatted array containing the success status, HTTP status, and data or error.
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
     * Send a message to a single phone number.
     *
     * @param string $phone The recipient's phone number.
     * @param string $message The message to send.
     * @param bool $verify Whether to verify the SSL certificate.
     * @param bool $sandbox Whether to use the sandbox environment.
     * @return array The formatted API response.
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
     * Generate a numeric One-Time Password (OTP).
     *
     * @param int $length The desired length of the OTP.
     * @return int The generated OTP.
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
     * Send an OTP message to a phone number.
     *
     * @param string $phone The recipient's phone number.
     * @param string $otp The OTP to send.
     * @param bool $verify Whether to verify the SSL certificate.
     * @param bool $sandbox Whether to use the sandbox environment.
     * @return array The formatted API response.
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
