<?php

namespace OctopusTeam\Waapi;

use Illuminate\Support\Facades\Http;

class Waapi
{
    protected string $url;
    protected string $appkey;
    protected string $authkey;

    public function __construct()
    {
        $this->url = config('waapi.app_url');
        $this->appkey = config('waapi.app_key');
        $this->authkey = config('waapi.auth_key');
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
