<?php

declare(strict_types=1);

namespace Talav\UserBundle\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Talav\Component\Resource\Exception\SafeMessageException;

trait AuthenticationExceptionTrait
{
    public function throwException(SafeMessageException $exception): never
    {
        $custom = new CustomUserMessageAuthenticationException(previous: $exception);
        $custom->setSafeMessage($exception->getMessageKey(), $exception->getMessageData());
        throw $custom;
    }
}
