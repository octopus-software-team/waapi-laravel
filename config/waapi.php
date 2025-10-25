<?php
return [
    /**
     * The base URL for the WAAPI service.
     * This is where your application will send requests to create messages.
     * Defaults to 'https://waapi.octopusteam.net/api/create-message'.
     */
    'app_url' => env('WAAPI_URL', 'https://waapi.octopusteam.net/api/create-message'),

    /**
     * Your unique application key provided by WAAPI.
     * This key is used to authenticate your application with the WAAPI service.
     */
    'app_key' => env('WAAPI_APP_KEY', ''),

    /**
     * Your authentication key for the WAAPI service.
     * This key is used to authorize your requests to the WAAPI service.
     */
    'auth_key' => env('WAAPI_AUTH_KEY', ''),

    'webhook' => [
        /**
         * The URL where WAAPI should send webhook notifications to your application.
         * Defaults to '/api/webhook/whatsapp'.
         */
        'url' => env('WAAPI_WEBHOOK_URL', '/api/webhook/whatsapp'),

        /**
         * Enable or disable webhook functionality.
         * If set to false, no webhooks will be processed or registered.
         */
        'enabled' => env('WAAPI_WEBHOOK_ENABLED', true),

        /**
         * Automatically register the webhook URL with the WAAPI service.
         * If true, the package will attempt to register the 'url' above with WAAPI.
         */
        'auto_register' => env('WAAPI_WEBHOOK_AUTO_REGISTER', true),

        /**
         * A token used to secure your webhook endpoint.
         * WAAPI will include this token in its webhook requests, allowing you to verify their authenticity.
         */
        'webhook_site_token' => env('WAAPI_WEBHOOK_SITE_TOKEN', null),

        /**
         * The URL for the webhook that WAAPI should call when a device's status is updated.
         * This is typically used for specific device-related events.
         */
        'waapi_update_device_webhook' => env('WAAPI_UPDATE_DEVICE_WEBHOOK', null),
    ],
];
