<?php

namespace OctopusTeam\Waapi;

use Illuminate\Support\Facades\Http;
class WaapiService
{
    protected string $url;
    protected string $appkey;
    protected string $authkey;

    public function __construct()
    {
        $this->url     = config('waapi.app_url');
        $this->appkey  = config('waapi.app_key');
        $this->authkey = config('waapi.auth_key');
    }

    public function SendMsg(string $phone, string $message,$verify=false,$sandbox=false)
    {
        return Http::withOptions([
            'verify' => $verify,
        ])->asForm()->post($this->url, [
            'appkey'  => $this->appkey,
            'authkey' => $this->authkey,
            'to'      => $phone,
            'message' => $message,
            'sandbox' => $sandbox,
        ]);
    }
}
