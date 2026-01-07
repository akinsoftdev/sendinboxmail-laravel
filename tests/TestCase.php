<?php

declare(strict_types=1);

namespace Akinsoft\SendInboxMail\Tests;

use Akinsoft\SendInboxMail\SendInboxMailServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            SendInboxMailServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('sendinboxmail.url', 'https://notify.sendinboxmail.com/manage/send.php');
        $app['config']->set('sendinboxmail.apikey', 'test-api-key');
        $app['config']->set('sendinboxmail.news_id', 1);
        $app['config']->set('sendinboxmail.sender_id', 1);
        $app['config']->set('sendinboxmail.timeout', 30);
        $app['config']->set('sendinboxmail.retry_times', 3);
    }
}
