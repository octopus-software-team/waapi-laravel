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
];