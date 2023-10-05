<?php

declare(strict_types=1);

namespace Talav\UserBundle\Message\Handler;

use Talav\Component\User\Model\UserInterface;
use Talav\Component\User\Repository\UserRepositoryInterface;
use Talav\UserBundle\Message\Command\RegisterLoginIpAddressCommand;
use Talav\UserBundle\Service\LoginAttemptService;
use Talav\UserBundle\Message\Event\LoginWithAnotherIpAddressEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RegisterLoginIpAddressHandler
{
    public function __construct(
        private readonly LoginAttemptService $loginAttempt,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function __invoke(RegisterLoginIpAddressCommand $command): void
    {
        $user = $command->user;
        $this->loginAttempt->deleteAttemptsFor($user);

        if ($user instanceof UserInterface) {
            if ($user->getLastLoginIp() !== $command->ip && null !== $user->getLastLoginAt()) {
                $this->dispatcher->dispatch(new LoginWithAnotherIpAddressEvent(
                    user: $user,
                    ip: $command->ip
                ));
            }

            $user->setLastLoginIp($command->ip)
                ->setLastLoginAt(new \DateTimeImmutable());
            $this->userRepository->save($user);
        }
    }
}
