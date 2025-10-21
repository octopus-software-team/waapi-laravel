<?php

return [
    'app_url' => env('WAAPI_URL', 'https://waapi.octopusteam.net/api/create-message'),
    'app_key' => env('WAAPI_APP_KEY', ''),
    'auth_key' => env('WAAPI_AUTH_KEY', ''),

    'webhook' => [
        'url' => env('WAAPI_WEBHOOK_URL', '/api/webhook/whatsapp'),
        'enabled' => env('WAAPI_WEBHOOK_ENABLED', true),
        'auto_register' => env('WAAPI_WEBHOOK_AUTO_REGISTER', true),

        // إعدادات Webhook.site للاختبار
        'webhook_site_token' => env('WAAPI_WEBHOOK_SITE_TOKEN', null),
    ],
];
