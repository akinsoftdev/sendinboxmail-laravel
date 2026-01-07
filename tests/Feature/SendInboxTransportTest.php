<?php

declare(strict_types=1);

use Akinsoft\SendInboxMail\Exceptions\SendInboxMailException;
use Akinsoft\SendInboxMail\Transport\SendInboxTransport;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Email;

beforeEach(function () {
    Http::preventStrayRequests();
});

it('can create transport instance', function () {
    $transport = new SendInboxTransport(
        url: 'https://notify.sendinboxmail.com/manage/send.php',
        apikey: 'test-key',
        newsId: 1,
        senderId: 1,
    );

    expect((string) $transport)->toBe('sendinboxmail');
});

it('registers the sendinboxmail mail driver', function () {
    config([
        'mail.mailers.sendinboxmail' => [
            'transport' => 'sendinboxmail',
        ],
    ]);

    $mailer = Mail::mailer('sendinboxmail');

    expect($mailer)->not->toBeNull();
});

it('sends email successfully', function () {
    Http::fake([
        '*' => Http::response([
            'Status_Code' => 0,
            'Status' => 'Success',
        ], 200),
    ]);

    config([
        'mail.mailers.sendinboxmail' => [
            'transport' => 'sendinboxmail',
        ],
        'mail.default' => 'sendinboxmail',
    ]);

    Mail::raw('Test email content', function ($message) {
        $message->to('test@example.com')
            ->subject('Test Subject');
    });

    Http::assertSentCount(1);
});

it('throws exception on failed response', function () {
    Http::fake([
        '*' => Http::response([
            'Status_Code' => 1,
            'Status' => 'Failed to send email',
        ], 200),
    ]);

    $transport = new SendInboxTransport(
        url: 'https://notify.sendinboxmail.com/manage/send.php',
        apikey: 'test-key',
        newsId: 1,
        senderId: 1,
        retryTimes: 1,
    );

    $email = (new Email)
        ->from('sender@example.com')
        ->to('recipient@example.com')
        ->subject('Test')
        ->html('<p>Test</p>');

    $sentMessage = new \Symfony\Component\Mailer\SentMessage(
        new \Symfony\Component\Mime\RawMessage($email->toString()),
        new \Symfony\Component\Mailer\Envelope(
            new \Symfony\Component\Mime\Address('sender@example.com'),
            [new \Symfony\Component\Mime\Address('recipient@example.com')],
        ),
    );

    // Use reflection to call protected method
    $reflection = new ReflectionClass($transport);
    $method = $reflection->getMethod('doSend');
    $method->setAccessible(true);

    expect(fn () => $method->invoke($transport, $sentMessage))
        ->toThrow(SendInboxMailException::class);
});

it('throws exception on connection error', function () {
    Http::fake([
        '*' => Http::response(null, 500),
    ]);

    $transport = new SendInboxTransport(
        url: 'https://notify.sendinboxmail.com/manage/send.php',
        apikey: 'test-key',
        newsId: 1,
        senderId: 1,
        retryTimes: 1,
    );

    $email = (new Email)
        ->from('sender@example.com')
        ->to('recipient@example.com')
        ->subject('Test')
        ->html('<p>Test</p>');

    $sentMessage = new \Symfony\Component\Mailer\SentMessage(
        new \Symfony\Component\Mime\RawMessage($email->toString()),
        new \Symfony\Component\Mailer\Envelope(
            new \Symfony\Component\Mime\Address('sender@example.com'),
            [new \Symfony\Component\Mime\Address('recipient@example.com')],
        ),
    );

    $reflection = new ReflectionClass($transport);
    $method = $reflection->getMethod('doSend');
    $method->setAccessible(true);

    expect(fn () => $method->invoke($transport, $sentMessage))
        ->toThrow(SendInboxMailException::class);
});
