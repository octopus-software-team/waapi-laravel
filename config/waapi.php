<?php



/*
  |--------------------------------------------------------------------------
  | WhatsApp API Configurations
  |--------------------------------------------------------------------------
  */
return [
    'app_url' => env('WAAPI_URL', 'https://waapi.octopusteam.net/api/create-message'),
    'app_key' => env('WAAPI_APP_KEY', ''),
    'auth_key' => env('WAAPI_AUTH_KEY', ''),

    // إضافة إعدادات الـ Webhook
    'webhook' => [
        'url' => env('WAAPI_WEBHOOK_URL', '/api/webhook/whatsapp'),
        'enable' => env('WAAPI_WEBHOOK_ENABLE', true),
        'auto_register' => env('WAAPI_WEBHOOK_AUTO_REGISTER', true),
    ],
];