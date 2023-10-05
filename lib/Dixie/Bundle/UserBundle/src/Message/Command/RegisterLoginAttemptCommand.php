<?php

declare(strict_types=1);

namespace Talav\UserBundle\Message\Command;

use Talav\Component\User\Model\UserInterface;

final class RegisterLoginAttemptCommand
{
    public function __construct(
        public readonly UserInterface $user
    ) {
    }
}
