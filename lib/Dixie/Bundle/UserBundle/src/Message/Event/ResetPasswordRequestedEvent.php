<?php

declare(strict_types=1);

namespace Talav\UserBundle\Message\Event;

use Talav\Component\User\Model\UserInterface;
use Talav\UserBundle\Entity\ResetPasswordToken;
final class ResetPasswordRequestedEvent
{
    public function __construct(
        public readonly UserInterface $user,
        public readonly ResetPasswordToken $token
    ) {
    }
}
