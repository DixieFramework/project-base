<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use SymfonyCasts\Bundle\ResetPassword\Exception\ExpiredResetPasswordTokenException;
use SymfonyCasts\Bundle\ResetPassword\Exception\InvalidResetPasswordTokenException;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\TooManyPasswordRequestsException;
use SymfonyCasts\Bundle\VerifyEmail\Exception\ExpiredSignatureException;
use SymfonyCasts\Bundle\VerifyEmail\Exception\InvalidSignatureException;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\WrongEmailVerifyException;

/**
 * Service to map registration and reset password exceptions.
 */
class UserExceptionService
{
    /**
     * Handle an exception by set the authentication error to the session.
     */
    public function handleException(Request $request, \Throwable $e): void
    {
        if ($request->hasSession()) {
            $exception = $this->mapException($e);
            $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);
        }
    }

    /**
     * Creates a custom user exception.
     */
    private function createException(string $message, \Throwable $previous = null, array $parameters = []): CustomUserMessageAuthenticationException
    {
        $code = (int) ($previous?->getCode() ?? 0);

        return new CustomUserMessageAuthenticationException($message, $parameters, $code, $previous);
    }

    /**
     * Map the given exception to a custom user exception.
     */
    private function mapException(\Throwable $e): CustomUserMessageAuthenticationException
    {
        if ($e instanceof ExpiredSignatureException) {
            return $this->createException('registration_expired_signature', $e);
        }
        if ($e instanceof InvalidSignatureException) {
            return $this->createException('registration_invalid_signature', $e);
        }
        if ($e instanceof WrongEmailVerifyException) {
            return $this->createException('registration_wrong_email_verify', $e);
        }
        if ($e instanceof VerifyEmailExceptionInterface) {
            return $this->createException($e->getReason(), $e);
        }
        if ($e instanceof ExpiredResetPasswordTokenException) {
            return $this->createException('reset_expired_reset_password_token', $e);
        }
        if ($e instanceof InvalidResetPasswordTokenException) {
            return $this->createException('reset_invalid_reset_password_token', $e);
        }
        if ($e instanceof TooManyPasswordRequestsException) {
            $parameters = ['%availableAt%' => $e->getAvailableAt()->format('H:i')];

            return $this->createException('reset_too_many_password_request', $e, $parameters);
        }
        if ($e instanceof ResetPasswordExceptionInterface) {
            return $this->createException($e->getReason(), $e);
        }
        if ($e instanceof TransportExceptionInterface) {
            return $this->createException('send_email_error', $e);
        }

        return $this->createException('error_unknown', $e);
    }
}
