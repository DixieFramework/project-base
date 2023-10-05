<?php

declare(strict_types=1);

namespace Talav\UserBundle\Message\Handler;

use Talav\UserBundle\Message\Command\RegisterLoginAttemptCommand;
use Talav\UserBundle\Service\LoginAttemptService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RegisterLoginAttemptHandler
{
    public function __construct(
        private readonly LoginAttemptService $loginAttempt,
    ) {
    }

    public function __invoke(RegisterLoginAttemptCommand $command): void
    {
        $this->loginAttempt->addAttempt($command->user);
    }
}
