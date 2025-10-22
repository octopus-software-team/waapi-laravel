# OctopusTeam WAAPI Laravel

Simple and flexible **WhatsApp API integration** for Laravel, built by Octopus Team.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/octopusteam/waapi-laravel.svg?style=flat-square)](https://packagist.org/packages/octopusteam/waapi-laravel) [![Total Downloads](https://img.shields.io/packagist/dt/octopusteam/waapi-laravel.svg?style=flat-square)](https://packagist.org/packages/octopusteam/waapi-laravel) [![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
![IMAGE](assets/cover.jpg)
---

## 📦 Installation

```bash
composer require octopusteam/waapi-laravel
```

Then publish the config file:

```bash
php artisan vendor:publish --provider="OctopusTeam\Waapi\WaapiServiceProvider" --tag="config"
```

This will create `config/waapi.php`.

---

## ⚙️ Configuration

Add or update the following environment variables in your `.env` file:

```env
WAAPI_URL=https://waapi.octopusteam.net/api/create-message
WAAPI_APP_KEY=your_app_key
WAAPI_AUTH_KEY=your_auth_key

# Webhook
WAAPI_WEBHOOK_URL=/api/webhook/whatsapp
WAAPI_WEBHOOK_ENABLED=true
WAAPI_WEBHOOK_AUTO_REGISTER=true

# Webhook.site (optional for testing)
WAAPI_WEBHOOK_SITE_TOKEN=your-webhook-site-token
WAAPI_UPDATE_DEVICE_WEBHOOK=your-device-uuid-from-waapi
```

You can also modify the default configuration in `config/waapi.php`.

---

## 🚀 Usage

### Handle Incoming Webhooks

Webhook route will be auto-registered if `WAAPI_WEBHOOK_ENABLED=true` and `WAAPI_WEBHOOK_AUTO_REGISTER=true` in your config.

You can handle it manually if you prefer:

```php
use Illuminate\Http\Request;
use OctopusTeam\Waapi\Facades\Waapi;

Route::post('/api/webhook/whatsapp', function (Request $request) {
    return Waapi::handleWebhook($request);
});
```

---

### Send a WhatsApp Message

```php
use OctopusTeam\Waapi\Facades\Waapi;

$response = Waapi::sendMessage('201234567890', 'Hello from Octopus Team 🚀');

if ($response['success']) {
    echo "Message sent successfully!";
} else {
    echo "Failed to send message.";
}
```

---

### Send an OTP

```php
use OctopusTeam\Waapi\Facades\Waapi;

$otp = Waapi::generateOtp(6);
$response = Waapi::sendOtp('201234567890', $otp);
```

---

### Work with Webhook.site (for testing)

```php
use OctopusTeam\Waapi\Facades\Waapi;

// Get webhook requests
$data = Waapi::getWebhookSiteData(50);

// Decode JSON contents automatically
$contents = Waapi::getWebhookSiteContent(50);

// Get a specific webhook request by ID
$request = Waapi::getWebhookSiteRequest('your-request-id');
```

---

## 🧭 Command: Renew Webhook Token

You can renew your Webhook.site token automatically with:

```bash
php artisan waapi:webhook-renew
```

This command will:
1. Create a new Webhook.site token.
2. Update your `.env` file automatically.
3. Notify WAAPI API to update the device’s webhook URL.

---

## ✅ Compatibility

| Laravel Version | PHP Version(s) | Supported |
|-----------------|------------------|------------|
| 12.x            | 8.3, 8.4         | ✅ |
| 11.x            | 8.2, 8.3         | ✅ |
| 10.x            | 8.1, 8.2, 8.3    | ✅ |
| 9.x             | 8.0, 8.1, 8.2    | ✅ |
| 8.x             | 7.4, 8.0, 8.1    | ⚠️ Works but not officially supported |

---

## 🧪 Testing

You can run basic tests included in `tests/Feature/WaapiTest.php`:

```bash
php artisan test
```

---

## 🤝 Contributing

Contributions are always welcome!

- Open issues or feature requests
- Submit pull requests
- Improve documentation

---

## 📜 License

This package is open-source software licensed under the [MIT license](LICENSE).

---

## ✨ Credits

- [Octopus Team](https://github.com/octopus-software-team)
- [Abdallah Mahmoud](https://github.com/eldapour)
