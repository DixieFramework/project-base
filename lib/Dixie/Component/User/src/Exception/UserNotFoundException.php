<?php

declare(strict_types=1);

namespace Talav\Component\User\Exception;

use Talav\Component\Resource\Exception\SafeMessageException;

final class UserNotFoundException extends SafeMessageException
{
    protected string $messageDomain = 'authentication';

    public function __construct(
        string $message = 'authentication.exceptions.user_not_found',
        array $messageData = [],
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $messageData, $code, $previous);
    }
}
