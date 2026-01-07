<?php

declare(strict_types=1);

namespace Akinsoft\SendInboxMail;

use Akinsoft\SendInboxMail\Transport\SendInboxMailTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class SendInboxMailServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/sendinboxmail.php',
            'sendinboxmail',
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPublishing();
        $this->registerMailTransport();
    }

    /**
     * Register the package's publishable resources.
     */
    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/sendinboxmail.php' => config_path('sendinboxmail.php'),
            ], 'sendinboxmail-config');
        }
    }

    /**
     * Register the SendInboxMail mail transport.
     */
    protected function registerMailTransport(): void
    {
        Mail::extend('sendinboxmail', function (array $config = []) {
            return new SendInboxMailTransport(
                url: $config['url'] ?? config('sendinboxmail.url'),
                apikey: $config['apikey'] ?? config('sendinboxmail.apikey'),
                newsId: (int) ($config['news_id'] ?? config('sendinboxmail.news_id')),
                senderId: (int) ($config['sender_id'] ?? config('sendinboxmail.sender_id')),
                timeout: (int) ($config['timeout'] ?? config('sendinboxmail.timeout', 30)),
                retryTimes: (int) ($config['retry_times'] ?? config('sendinboxmail.retry_times', 3)),
            );
        });
    }
}
