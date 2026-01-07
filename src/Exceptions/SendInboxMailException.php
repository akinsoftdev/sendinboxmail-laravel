<?php

declare(strict_types=1);

namespace Akinsoft\SendInboxMail\Exceptions;

use Exception;
use Throwable;

class SendInboxMailException extends Exception
{
    /**
     * Create a new SendInboxMail exception instance.
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            "[SendInboxMail] {$message}",
            $code,
            $previous,
        );
    }
}
