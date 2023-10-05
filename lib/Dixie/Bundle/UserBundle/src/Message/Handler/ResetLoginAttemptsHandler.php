<?php

declare(strict_types=1);

namespace Talav\UserBundle\Message\Handler;

use Talav\Component\User\Model\UserInterface;
use Talav\Component\User\Repository\UserRepositoryInterface;
use Talav\UserBundle\Message\Command\ResetLoginAttemptsCommand;
use Talav\UserBundle\Service\LoginAttemptService;
use Talav\Component\User\Exception\InvalidResetLoginAttemptsTokenException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ResetLoginAttemptsHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly LoginAttemptService $loginAttemptService
    ) {
    }

    public function __invoke(ResetLoginAttemptsCommand $command): void
    {
        /** @var UserInterface|null $user */
        $user = $this->userRepository->findOneByCaseInsensitive([
            'loginAttemptsResetToken' => $command->token,
        ]);

        if (null === $user) {
            throw new InvalidResetLoginAttemptsTokenException();
        }

        $this->loginAttemptService->deleteAttemptsFor($user);
    }
}
