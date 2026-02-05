# Barta (কথা) - The unified interface for every Bangladeshi SMS gateway.
<div align="center">
<a href="https://github.com/iRaziul/barta">
<img src="https://raw.githubusercontent.com/iRaziul/barta/main/.github/assets/banner.svg" alt="Barta Banner">
</a>
<br>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/larament/barta.svg?style=flat-square)](https://packagist.org/packages/larament/barta)
[![Total Downloads](https://img.shields.io/packagist/dt/larament/barta.svg?style=flat-square)](https://packagist.org/packages/larament/barta)
[![Run Tests](https://github.com/iRaziul/barta/actions/workflows/run-tests.yml/badge.svg)](https://github.com/iRaziul/barta/actions/workflows/run-tests.yml)
[![PHPStan](https://github.com/iRaziul/barta/actions/workflows/phpstan.yml/badge.svg)](https://github.com/iRaziul/barta/actions/workflows/phpstan.yml)
[![Pint](https://github.com/iRaziul/barta/actions/workflows/fix-php-code-style-issues.yml/badge.svg)](https://github.com/iRaziul/barta/actions/workflows/fix-php-code-style-issues.yml)
[![License](https://img.shields.io/github/license/iRaziul/barta.svg?style=flat-square)](https://github.com/iRaziul/barta/blob/main/LICENSE.md)
</div>

---

## Introduction
Barta is a clean, expressive Laravel package designed to integrate popular Bangladeshi SMS gateways seamlessly. Whether you're sending OTPs, marketing alerts, or notifications, Barta makes the process as simple as a conversation.

## Key Features

- **Multiple Gateways** — Seamlessly switch between SMS providers
- **Bulk SMS** — Send to multiple recipients in a single call
- **Queue Support** — Dispatch SMS to background jobs
- **Laravel Notifications** — Native integration with Laravel's notification system
- **BD Phone Formatting** — Automatic phone number normalization to `8801XXXXXXXXX` format
- **Extensible** — Create custom drivers for any SMS gateway


## Installation

Install via Composer:

```bash
composer require larament/barta
```

Optionally, you can run the install command:

```bash
php artisan barta:install
```

---

## Configuration

Set your default driver and add credentials to `.env`:

```env
BARTA_DRIVER=log
```

Each gateway requires different credentials. See [Gateways](https://barta.larament.com/gateways/) for all available options and environment variable names.
> [!TIP]
> Use `log` driver during development to avoid sending real SMS.

---

## Usage

```php
use Larament\Barta\Facades\Barta;

// Send SMS
Barta::to('01712345678')
    ->message('Your OTP is 1234')
    ->send();

// Use a specific gateway
Barta::driver('esms')
    ->to('01712345678')
    ->message('Hello!')
    ->send();

// Bulk SMS
Barta::to(['01712345678', '01812345678'])
    ->message('Hello everyone!')
    ->send();

// Queue for background processing
Barta::to('01712345678')
    ->message('Queued message')
    ->queue();
```

📚 **[Full Usage Guide →](https://barta.larament.com/usage/basic-usage/)**

---

## Laravel Notifications

```php
use Larament\Barta\Notifications\BartaMessage;

class OrderShipped extends Notification
{
    public function via($notifiable): array
    {
        return ['barta'];
    }

    public function toBarta($notifiable): BartaMessage
    {
        return new BartaMessage("Your order has been shipped!");
    }
}
```

Add the route to your model:

```php
public function routeNotificationForBarta($notification): string
{
    return $this->phone;
}
```

📚 **[Full Notifications Guide →](https://barta.larament.com/advanced/notifications/)**

---

## Phone Number Formatting

Barta automatically normalizes Bangladeshi phone numbers to `8801XXXXXXXXX` format:

| Input | Output |
| ----- | ------ |
| `01712345678` | `8801712345678` |
| `+8801712345678` | `8801712345678` |

---

## Testing

```bash
composer test          # Run tests
composer test-coverage # With coverage
composer analyse       # Static analysis
```

Use the `log` driver during testing to avoid sending real SMS.

---

## Documentation

For complete documentation including custom drivers, error handling, and all gateway configurations:

**📚 [barta.larament.com](https://barta.larament.com)**

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for recent changes.

## Contributing

See [CONTRIBUTING.md](.github/CONTRIBUTING.md) for details.

## Security

Report vulnerabilities via our [security policy](.github/SECURITY.md).

## Credits

- [Raziul Islam](https://github.com/iRaziul)
- [All Contributors](../../contributors)

## License

MIT License. See [LICENSE.md](LICENSE.md).

---

<div align="center">
<p>Made with ❤️ for the Bangladeshi Laravel Community</p>
<p>
<a href="https://github.com/iRaziul/barta/stargazers">⭐ Star us on GitHub</a>
</p>
</div>
