<?php

declare(strict_types=1);

namespace Talav\Component\User\Exception;

use Talav\Component\Resource\Exception\SafeMessageException;

final class TooManyLoginAttemptsException extends SafeMessageException
{
    protected string $messageDomain = 'TalavUserBundle';

    public function __construct(
        string $message = 'authentication.exceptions.too_many_login_attempts',
        array $messageData = [],
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $messageData, $code, $previous);
    }
}
