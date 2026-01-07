# SendInboxMail Laravel Mail Transport

[![Latest Version on Packagist](https://img.shields.io/packagist/v/akinsoftdev/sendinboxmail-laravel.svg?style=flat-square)](https://packagist.org/packages/akinsoftdev/sendinboxmail-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/akinsoftdev/sendinboxmail-laravel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/akinsoftdev/sendinboxmail-laravel/actions?query=workflow%3Atests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/akinsoftdev/sendinboxmail-laravel.svg?style=flat-square)](https://packagist.org/packages/akinsoftdev/sendinboxmail-laravel)
[![License](https://img.shields.io/packagist/l/akinsoftdev/sendinboxmail-laravel.svg?style=flat-square)](https://packagist.org/packages/akinsoftdev/sendinboxmail-laravel)

A seamless Laravel mail transport driver for [SendInboxMail](https://www.sendinboxmail.com) email service. This package allows you to send emails through SendInboxMail API with full Laravel Mail integration.

## Requirements

- PHP 8.1 or higher
- Laravel 10.x, 11.x, or 12.x

## Installation

You can install the package via composer:

```bash
composer require akinsoftdev/sendinboxmail-laravel
```

The package will automatically register its service provider.

## Configuration

### 1. Publish the Configuration File

```bash
php artisan vendor:publish --tag="sendinboxmail-config"
```

This will create a `config/sendinboxmail.php` file in your application.

### 2. Environment Variables

Add the following variables to your `.env` file:

```env
SENDINBOXMAIL_URL=https://your-sendinboxmail-api-url.com/send
SENDINBOXMAIL_API_KEY=your-api-key
SENDINBOXMAIL_NEWS_ID=1
SENDINBOXMAIL_SENDER_ID=1
SENDINBOXMAIL_TIMEOUT=30
SENDINBOXMAIL_RETRY_TIMES=3
```

### 3. Configure Mail Driver

Add the SendInboxMail mailer to your `config/mail.php` file:

```php
'mailers' => [
    // ... other mailers

    'sendinboxmail' => [
        'transport' => 'sendinboxmail',
    ],
],
```

### 4. Set as Default (Optional)

To use SendInboxMail as your default mailer, update your `.env`:

```env
MAIL_MAILER=sendinboxmail
```

## Usage

### Using as Default Mailer

Once configured as default, all your emails will be sent through SendInboxMail:

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;

Mail::to('user@example.com')->send(new WelcomeEmail());
```

### Using Explicitly

You can also explicitly use the SendInboxMail mailer:

```php
Mail::mailer('sendinboxmail')
    ->to('user@example.com')
    ->send(new WelcomeEmail());
```

### Sending Raw Email

```php
Mail::mailer('sendinboxmail')->raw('Hello World!', function ($message) {
    $message->to('user@example.com')
        ->subject('Test Email');
});
```

### With Mailable Class

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Our Platform',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
        );
    }
}
```

## Inline Attachments

The package supports inline attachments (embedded images):

```php
<img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="Logo">
```

## Configuration Options

| Option | Description | Default |
|--------|-------------|---------|
| `url` | SendInboxMail API endpoint URL | https://notify.sendinboxmail.com/manage/send.php |
| `apikey` | Your SendInboxMail API key | - |
| `news_id` | Campaign/News identifier | - |
| `sender_id` | Sender profile identifier | - |
| `timeout` | Request timeout in seconds | 30 |
| `retry_times` | Number of retry attempts | 3 |

## Error Handling

The package throws `SendInboxMailException` for any API errors:

```php
use Akinsoft\SendInboxMail\Exceptions\SendInboxMailException;

try {
    Mail::mailer('sendinboxmail')->to('user@example.com')->send(new WelcomeEmail());
} catch (SendInboxMailException $e) {
    // Handle the exception
    Log::error('SendInboxMail Error: ' . $e->getMessage());
}
```

## Testing

```bash
composer test
```

## Code Style

This package uses [Laravel Pint](https://laravel.com/docs/pint) for code styling:

```bash
composer format
```

## Static Analysis

Run PHPStan for static analysis:

```bash
composer analyse
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Security Vulnerabilities

If you discover a security vulnerability, please send an email to programlama@akinsoft.com.tr. All security vulnerabilities will be promptly addressed.

## Credits

- [AKINSOFT](https://www.akinsoft.com.tr)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

