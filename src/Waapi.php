<?php

namespace OctopusTeam\Waapi;

use Illuminate\Support\Facades\Http;

class Waapi
{
    protected string $url;
    protected string $appkey;
    protected string $authkey;

    /**
     * Waapi constructor.
     */
    public function __construct()
    {
        $this->url = config('waapi.app_url');
        $this->appkey = config('waapi.app_key');
        $this->authkey = config('waapi.auth_key');
    }

    /**
     * @param string $phone
     * @param string $message
     * @param bool $verify
     * @param bool $sandbox
     * @return mixed
     * send a message to a single phone
     */
    public function sendMessage(string $phone, string $message, bool $verify = false, bool $sandbox = false): mixed
    {
        return Http::withOptions([
            'verify' => $verify,
        ])->asForm()->post($this->url, [
            'appkey' => $this->appkey,
            'authkey' => $this->authkey,
            'to' => $phone,
            'message' => $message,
            'sandbox' => $sandbox,
        ]);
    }

    /**
     * @param array $phones
     * @param string $message
     * @param bool $verify
     * @param bool $sandbox
     * @return array
     * send bulk messages to multiple phones
     */
    public function sendBulkMessages(array $phones, string $message, bool $verify = false, bool $sandbox = false): array
    {
        $responses = [];

        foreach ($phones as $phone) {
            $responses[] = Http::withOptions([
                'verify' => $verify,
            ])->asForm()->post($this->url, [
                'appkey' => $this->appkey,
                'authkey' => $this->authkey,
                'to' => $phone,
                'message' => $message,
                'sandbox' => $sandbox,
            ]);
        }

        return $responses;
    }

    /**
     * @param $length
     * @return int
     * @throws \Exception
     * generate OTP for the user before sending a message
     */
    public function generateOtp($length = 6): int
    {
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= mt_rand(0, 9);
        }
        return $otp;
    }

    public function sendOtp(string $phone, string $otp, bool $verify = false, bool $sandbox = false): mixed
    {
        return Http::withOptions([
            'verify' => $verify,
        ])->asForm()->post($this->url, [
            'appkey' => $this->appkey,
            'authkey' => $this->authkey,
            'to' => $phone,
            'message' => "Your OTP is: ({$otp}). Do not share this code with anyone.",
            'sandbox' => $sandbox,
        ]);
    }
}
