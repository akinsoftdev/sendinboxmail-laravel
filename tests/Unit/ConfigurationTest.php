<?php

declare(strict_types=1);

it('has correct default configuration values', function () {
    expect(config('sendinboxmail.url'))->toBe('https://notify.sendinboxmail.com/manage/send.php');
    expect(config('sendinboxmail.apikey'))->toBe('test-api-key');
    expect(config('sendinboxmail.news_id'))->toBe(1);
    expect(config('sendinboxmail.sender_id'))->toBe(1);
    expect(config('sendinboxmail.timeout'))->toBe(30);
    expect(config('sendinboxmail.retry_times'))->toBe(3);
});

it('can override configuration values', function () {
    config(['sendinboxmail.timeout' => 60]);
    config(['sendinboxmail.retry_times' => 5]);

    expect(config('sendinboxmail.timeout'))->toBe(60);
    expect(config('sendinboxmail.retry_times'))->toBe(5);
});
