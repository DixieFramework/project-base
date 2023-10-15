<?php

declare(strict_types=1);

namespace Talav\UserBundle\Message\Handler;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Talav\Component\User\Repository\UserRepositoryInterface;
use Talav\UserBundle\Message\Command\UnbanUserCommand;
use Talav\UserBundle\Message\Event\UserUnbannedEvent;

#[AsMessageHandler]
final class UnbanUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface  $userRepository,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public function __invoke(UnbanUserCommand $command): void
    {
        $this->userRepository->save(
            $command->user
                ->setIsBanned(false)
                ->setBannedAt(null)
        );
        $this->dispatcher->dispatch(new UserUnbannedEvent($command->user));
    }
}
