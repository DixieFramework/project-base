<?php

declare(strict_types=1);

namespace Talav\Component\User\Exception;

use Talav\Component\Resource\Exception\SafeMessageException;

final class InvalidResetLoginAttemptsTokenException extends SafeMessageException
{
    protected string $messageDomain = 'authentication';

    public function __construct(
        string $message = 'authentication.exceptions.invalid_reset_login_attempts_token',
        array $messageData = [],
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $messageData, $code, $previous);
    }
}
