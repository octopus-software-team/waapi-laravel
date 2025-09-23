[![Latest Version on Packagist](https://img.shields.io/packagist/v/octopusteam/waapi-laravel.svg?style=flat-square)](https://packagist.org/packages/octopusteam/waapi-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/octopusteam/waapi-laravel.svg?style=flat-square)](https://packagist.org/packages/octopusteam/waapi-laravel)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![https://waapi.octopusteam.net](assets/waapi.png)](https://waapi.octopusteam.net)
Simple and flexible **WhatsApp API integration** for Laravel, built by [Octopus Team](https://github.com/octopus-software-team).  
This package provides an easy way to send WhatsApp messages using WAAPI.

---

## 📦 Installation

Install via [Composer](https://getcomposer.org):

```bash
composer require octopusteam/waapi-laravel
```

---

## ⚙️ Configuration

1. Publish the config file:

```bash
php artisan vendor:publish --provider="OctopusTeam\Waapi\WaapiServiceProvider" --tag="config"
```

2. A new config file will be created at:

```
config/waapi.php
```

3. Update your `.env` with your WAAPI credentials with the following variables in config/waapi.php:

```env
WAAPI_URL="https://api.example.com/v1/"
WAAPI_APP_KEY=xxxxxxxxxxxxxxxxx
WAAPI_AUTH_KEY=xxxxxxxxxxxxxxxx
```

### 🔑 Get Your API Key

To use this package, you need to create an account and generate your **WAAPI keys**:

1. Go to 👉 [https://waapi.octopusteam.net](https://waapi.octopusteam.net/pricing)
2. Sign up for a free trial (7 days available 🚀)
3. Choose a subscription plan (affordable options for continued API access)
4. After login, go to **My Apps** → **Integration**
5. Copy your `appkey` and `authkey` from the dashboard
---

## 🚀 Usage

### Send WhatsApp Message

```php
use OctopusTeam\Waapi\Facades\Waapi;

$phone   = '201234567890';
$message = 'Hello from Octopus Team 🚀';

$response = Waapi::sendMessage($phone, $message);

if ($response->successful()) {
    echo "Message sent successfully!";
} else {
    echo "Failed to send message.";
}
```

---

## 🧪 Testing

This package comes with a basic test example.

Run tests with:

```bash
php artisan test
```

---

## 📖 Example Test

See `tests/Feature/WaapiTest.php` for a sample test:

```php
$response = Waapi::sendMessage('201234567890', 'Hello from Waapi Test 🚀');
$this->assertNotNull($response);
```

---

## 🤝 Contributing

Contributions are welcome!  
Please open issues and submit pull requests to help improve this package.

---

## 📜 License

This package is open-sourced software licensed under the [MIT license](LICENSE).

---

## ✨ Credits

- [Octopus Team](https://github.com/octopus-software-team)
- [Abdallah Mahmoud](https://github.com/eldapour)
