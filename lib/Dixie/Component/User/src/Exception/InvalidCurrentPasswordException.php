<?php

declare(strict_types=1);

namespace Talav\Component\User\Exception;

use Talav\Component\Resource\Exception\SafeMessageException;

final class InvalidCurrentPasswordException extends SafeMessageException
{
    protected string $messageDomain = 'authentication';

    public function __construct(
        string $message = 'authentication.exceptions.invalid_current_password',
        array $messageData = [],
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $messageData, $code, $previous);
    }
}
