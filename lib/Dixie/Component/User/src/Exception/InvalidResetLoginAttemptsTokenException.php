<?php

declare(strict_types=1);

namespace Talav\Component\User\Exception;

use Devscast\Bundle\DddBundle\Domain\Exception\SafeMessageException;

/**
 * class InvalidResetLoginAttemptsTokenException.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
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
