<div align="center">
<a href="https://github.com/iRaziul/kotha">
<img src="https://raw.githubusercontent.com/iRaziul/kotha/main/.github/assets/banner.svg" alt="Kotha Banner">
</a>
<br>
<h1>Kotha (কথা)</h1>
<p>
    <strong>The unified interface for every Bangladeshi SMS gateway.</strong>
</p>
<p>
Kotha is a clean, expressive Laravel package designed to integrate popular Bangladeshi SMS gateways seamlessly. Whether you're sending OTPs, marketing alerts, or notifications, Kotha makes the process as simple as a conversation.
</p>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/larament/kotha.svg?style=flat-square)](https://packagist.org/packages/larament/kotha)
[![Total Downloads](https://img.shields.io/packagist/dt/larament/kotha.svg?style=flat-square)](https://packagist.org/packages/larament/kotha)
[![Run Tests](https://github.com/iRaziul/kotha/actions/workflows/run-tests.yml/badge.svg)](https://github.com/iRaziul/kotha/actions/workflows/run-tests.yml)
[![PHPStan](https://github.com/iRaziul/kotha/actions/workflows/phpstan.yml/badge.svg)](https://github.com/iRaziul/kotha/actions/workflows/phpstan.yml)
[![Pint](https://github.com/iRaziul/kotha/actions/workflows/fix-php-code-style-issues.yml/badge.svg)](https://github.com/iRaziul/kotha/actions/workflows/fix-php-code-style-issues.yml)
[![License](https://img.shields.io/github/license/iRaziul/kotha.svg?style=flat-square)](https://github.com/iRaziul/kotha/blob/main/LICENSE.md)

</div>

---

## ✨ Features

- **Multiple Gateways** — Seamlessly switch between SMS providers
- **Bulk SMS** — Send to multiple recipients in a single call
- **Queue Support** — Dispatch SMS to background jobs
- **Laravel Notifications** — Native integration with Laravel's notification system
- **BD Phone Formatting** — Automatic phone number normalization to `8801XXXXXXXXX` format
- **Extensible** — Create custom drivers for any SMS gateway

## 📦 Supported Gateways

| Gateway                      | Status       |
| ---------------------------- | ------------ |
| Log (Development)            | ✅ Built-in  |
| [eSMS](https://esms.com.bd)  | ✅ Supported |
| [MimSMS](https://mimsms.com) | ✅ Supported |

> **Want more gateways?** [Request a gateway](https://github.com/iRaziul/kotha/issues) or [contribute a driver](#-creating-custom-drivers).

---

## 🚀 Installation

Install via Composer:

```bash
composer require larament/kotha
```

Run the install command (publishes config + optional setup):

```bash
php artisan kotha:install
```

Or publish the config manually:

```bash
php artisan vendor:publish --tag="kotha-config"
```

---

## ⚙️ Configuration

Add your gateway credentials to `.env`:

```env
KOTHA_DRIVER=esms

# eSMS
KOTHA_ESMS_TOKEN=your-esms-api-token
KOTHA_ESMS_SENDER_ID=your-sender-id

# MimSMS
KOTHA_MIMSMS_USERNAME=your-username
KOTHA_MIMSMS_API_KEY=your-api-key
KOTHA_MIMSMS_SENDER_ID=your-sender-id

# Request Settings (Optional)
KOTHA_REQUEST_TIMEOUT=10
KOTHA_REQUEST_RETRY=3
KOTHA_REQUEST_RETRY_DELAY=300
```

> 💡 **Tip:** Use `log` driver during development to avoid sending real SMS.

---

## 🛠 Usage

### Basic Usage

```php
use Larament\Kotha\Facades\Kotha;

// Send SMS using the default driver
Kotha::to('01712345678')
    ->message('Your OTP is 1234')
    ->send();
```

### Specifying a Driver

```php
// Use a specific gateway
Kotha::driver('esms')
    ->to('01712345678')
    ->message('Hello from eSMS!')
    ->send();
```

### Bulk SMS

Send to multiple recipients in a single API call:

```php
Kotha::to(['01712345678', '01812345678', '01912345678'])
    ->message('Hello everyone!')
    ->send();
```

### Getting the Response

```php
$response = Kotha::to('01712345678')
    ->message('Hello!')
    ->send();

// Check if successful
if ($response->success) {
    // Access raw API response
    $data = $response->data;
}

// Convert to array
$array = $response->toArray();
// ['success' => true, 'data' => [...], 'errors' => []]
```

---

## ⏱️ Queue Support

Dispatch SMS to your queue for background processing:

```php
// Queue with default settings
Kotha::to('01712345678')
    ->message('This will be queued')
    ->queue();

// Specify queue name
Kotha::to('01712345678')
    ->message('Priority message')
    ->queue('sms');

// Specify connection and queue
Kotha::to('01712345678')
    ->message('Redis queue')
    ->queue('sms', 'redis');

// Bulk queued SMS
Kotha::to(['01712345678', '01812345678'])
    ->message('Queued bulk message')
    ->queue();
```

---

## 🔔 Laravel Notifications

Kotha integrates seamlessly with Laravel's notification system.

### 1. Create a Notification

```php
use Illuminate\Notifications\Notification;
use Larament\Kotha\Notifications\KothaMessage;

class OrderShipped extends Notification
{
    public function via($notifiable): array
    {
        return ['kotha'];
    }

    public function toKotha($notifiable): KothaMessage
    {
        return new KothaMessage(
            "Hi {$notifiable->name}, your order has been shipped!"
        );
    }
}
```

### 2. Add Route to Your Model

```php
// app/Models/User.php
public function routeNotificationForKotha($notification): string
{
    return $this->phone;
}
```

### 3. Send the Notification

```php
$user->notify(new OrderShipped());
```

### Using a Specific Driver

Override the default driver per notification:

```php
class OrderShipped extends Notification
{
    public function toKotha($notifiable): KothaMessage
    {
        return new KothaMessage('Your order shipped!');
    }

    // Optional: specify driver for this notification
    public function kothaDriver(): string
    {
        return 'mimsms';
    }
}
```

---

## 🔧 Creating Custom Drivers

Extend Kotha to support any SMS gateway.

### 1. Create the Driver Class

```php
namespace App\Sms\Drivers;

use Illuminate\Support\Facades\Http;
use Larament\Kotha\Drivers\AbstractDriver;
use Larament\Kotha\Data\ResponseData;
use Larament\Kotha\Exceptions\KothaException;

class CustomGatewayDriver extends AbstractDriver
{
    public function send(): ResponseData
    {
        $this->validate();

        $response = Http::withToken($this->config['api_token'])
            ->post('https://api.customgateway.com/sms', [
                'to' => implode(',', $this->recipients),
                'message' => $this->message,
                'sender' => $this->config['sender_id'],
            ])
            ->json();

        if ($response['status'] !== 'success') {
            throw new KothaException($response['error']);
        }

        return new ResponseData(
            success: true,
            data: $response,
        );
    }
}
```

### 2. Register the Driver

```php
// app/Providers/AppServiceProvider.php
use App\Sms\Drivers\CustomGatewayDriver;
use Larament\Kotha\Facades\Kotha;

public function boot(): void
{
    Kotha::extend('custom', function ($app) {
        return new CustomGatewayDriver(config('kotha.drivers.custom'));
    });
}
```

### 3. Add Configuration

```php
// config/kotha.php
'drivers' => [
    // ...existing drivers...

    'custom' => [
        'api_token' => env('CUSTOM_SMS_TOKEN'),
        'sender_id' => env('CUSTOM_SMS_SENDER_ID'),
    ],
],
```

### 4. Use Your Driver

```php
Kotha::driver('custom')
    ->to('01712345678')
    ->message('Hello from custom gateway!')
    ->send();
```

---

## 📞 Phone Number Formatting

Kotha automatically normalizes Bangladeshi phone numbers to the `8801XXXXXXXXX` format:

| Input            | Output          |
| ---------------- | --------------- |
| `1712345678`     | `8801712345678` |
| `01712345678`    | `8801712345678` |
| `8801712345678`  | `8801712345678` |
| `+8801712345678` | `8801712345678` |
| `017-1234-5678`  | `8801712345678` |

Invalid numbers will throw a `KothaException`.

---

## 🧪 Testing

```bash
# Run tests
composer test

# Run tests with coverage
composer test-coverage

# Run static analysis
composer analyse
```

### Testing in Your Application

Use the `log` driver during testing to avoid sending real SMS:

```php
// phpunit.xml or .env.testing
KOTHA_DEFAULT_DRIVER=log
```

---

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING.md](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please see our [security policy](.github/SECURITY.md) for details on how to report security vulnerabilities.

## Credits

- [Raziul Islam](https://github.com/iRaziul)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [LICENSE.md](LICENSE.md) for more information.

---

<div align="center">
<p>Made with ❤️ for the Bangladeshi Laravel Community</p>
<p>
<a href="https://github.com/iRaziul/kotha/stargazers">⭐ Star us on GitHub</a>
</p>
</div>
