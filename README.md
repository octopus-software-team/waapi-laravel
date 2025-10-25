# WAAPI Laravel Package

![WAAPI Logo](assets/cover.jpg)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/octopusteam/waapi-laravel.svg?style=flat-square)](https://packagist.org/packages/octopusteam/waapi-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/octopusteam/waapi-laravel.svg?style=flat-square)](https://packagist.org/packages/octopusteam/waapi-laravel)
[![License](https://img.shields.io/packagist/l/octopusteam/waapi-laravel.svg?style=flat-square)](https://packagist.org/packages/octopusteam/waapi-laravel)

This package provides a simple and expressive API for interacting with the WAAPI (WhatsApp API) service within a Laravel application.

## Features

- Send text messages and OTPs.
- Fluent and expressive API.
- Automatic webhook route registration.
- Handlers for incoming webhook data.
- Integration with Webhook.site for easy debugging.
- Artisan command to renew Webhook.site token.

## Installation

```bash
composer require octopusteam/waapi-laravel
```

Publish the configuration file:

```bash
php artisan vendor:publish --provider="OctopusTeam\Waapi\WaapiServiceProvider"
```

This will create a `config/waapi.php` file in your application.

## Configuration

Update your `.env` file with your WAAPI credentials:

```
WAAPI_APP_URL=https://waapi.octopusteam.net/api/v1/message/send
WAAPI_APP_KEY=your_app_key
WAAPI_AUTH_KEY=your_auth_key
WAAPI_WEBHOOK_SITE_TOKEN=your_webhook_site_token
WAAPI_UPDATE_DEVICE_WEBHOOK=your_device_uuid_for_webhook_update
```

## Usage

### Sending Messages

You can send messages using the `Waapi` facade or by injecting the `Waapi` class.

```php
use OctopusTeam\Waapi\Facades\Waapi;

// Send a simple text message
$response = Waapi::sendMessage('1234567890', 'Hello, world!');

// Send an OTP
$otp = Waapi::generateOtp();
$response = Waapi::sendOtp('1234567890', $otp);
```

### Webhook Handling

The package can automatically register a webhook route to handle incoming data from WAAPI. To enable this, ensure the following is in your `config/waapi.php`:

```php
'webhook' => [
    'enabled' => true,
    'auto_register' => true,
],
```

By default, the route is `POST /api/webhook/whatsapp`. You can customize the logic for handling webhooks in the `Waapi` class's `handleWebhook` method.

### Webhook.site Integration

For development and debugging, you can use the Webhook.site integration to inspect incoming webhook data.

```php
// Get the last 50 requests from Webhook.site
$data = Waapi::getWebhookSiteData(50);

// Get the decoded content from the last 50 requests
$content = Waapi::getWebHookSiteContent(50);

// Get a specific request by its ID
$request = Waapi::getWebhookSiteRequest('request-uuid');
```

### Artisan Command

To renew your `webhook.site` token automatically, you can run the following Artisan command. This will generate a new token, update your `.env` file, and update the webhook URL via the WAAPI service.

```bash
php artisan waapi:webhook-renew
```
