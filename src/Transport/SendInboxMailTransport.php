<?php

declare(strict_types=1);

namespace Akinsoft\SendInboxMail\Transport;

use Akinsoft\SendInboxMail\Exceptions\SendInboxMailException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Component\Mime\Part\DataPart;
use Throwable;

class SendInboxMailTransport extends AbstractTransport
{
    /**
     * Create a new SendInboxMail transport instance.
     */
    public function __construct(
        protected string $url,
        protected string $apikey,
        protected int $newsId,
        protected int $senderId,
        protected int $timeout = 30,
        protected int $retryTimes = 3,
    ) {
        parent::__construct();
    }

    /**
     * Send the given message.
     *
     * @throws SendInboxMailException
     */
    protected function doSend(SentMessage $message): void
    {
        $originalMessage = $message->getOriginalMessage();

        if ($originalMessage instanceof Email) {
            $email = $originalMessage;
        } elseif ($originalMessage instanceof Message) {
            $email = MessageConverter::toEmail($originalMessage);
        } else {
            throw new SendInboxMailException('Unsupported message type: '.get_class($originalMessage));
        }

        $attachments = collect($email->getAttachments());
        $inlineAttachments = $attachments->filter(
            fn ($attachment) => $attachment->getDisposition() === 'inline',
        );

        $formData = $this->buildFormData($email, $inlineAttachments);

        $this->sendRequest($formData);
    }

    /**
     * Build the form data array for the API request.
     *
     * @param  Collection<int, DataPart>  $inlineAttachments
     *
     * @return array<string, mixed>
     */
    protected function buildFormData(Email $email, Collection $inlineAttachments): array
    {
        $formData = [
            'email' => collect($email->getTo())->map(
                fn ($address) => $address->getAddress(),
            )->first(),
            'apikey' => $this->apikey,
            'news_id' => $this->newsId,
            'sender_id' => $this->senderId,
            'subject' => base64_encode($email->getSubject() ?? ''),
            'spc1' => $email->getHtmlBody(),
        ];

        foreach ($inlineAttachments as $index => $attachment) {
            $key = $index === 0 ? '' : '_'.($index + 1);

            $formData["send_file_inline_base64{$key}"] = $attachment->bodyToString();
            $formData["send_file_inline_filename{$key}"] = $attachment->getFilename();
            $formData["send_file_inline_content_type{$key}"] = base64_encode($attachment->getContentType());
        }

        return $formData;
    }

    /**
     * Send the HTTP request to SendInboxMail API.
     *
     * @param  array<string, mixed>  $formData
     *
     * @throws SendInboxMailException
     */
    protected function sendRequest(array $formData): void
    {
        try {
            $response = Http::asForm()
                ->timeout($this->timeout)
                ->retry($this->retryTimes, fn (int $attempt) => $attempt * 1000)
                ->throw(function (Response $response, RequestException $e) {
                    throw new SendInboxMailException('HTTP error: '.$e->getMessage());
                })
                ->post($this->url, $formData);

            $this->validateResponse($response->json());
        } catch (ConnectionException $e) {
            throw new SendInboxMailException('Connection failed: '.$e->getMessage(), 0, $e);
        } catch (SendInboxMailException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new SendInboxMailException('Unexpected error: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Validate the API response.
     *
     * @param  array<string, mixed>|null  $data
     *
     * @throws SendInboxMailException
     */
    protected function validateResponse(?array $data): void
    {
        if (! isset($data['Status_Code']) || $data['Status_Code'] != 0) {
            throw new SendInboxMailException(
                'Response failed: '.($data['Status'] ?? 'Invalid response format'),
            );
        }
    }

    /**
     * Get the string representation of the transport.
     */
    public function __toString(): string
    {
        return 'sendinboxmail';
    }
}
