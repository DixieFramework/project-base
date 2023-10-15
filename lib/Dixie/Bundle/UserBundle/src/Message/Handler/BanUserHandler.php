<?php

declare(strict_types=1);

namespace Talav\UserBundle\Message\Handler;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Talav\Component\User\Repository\UserRepositoryInterface;
use Talav\UserBundle\Message\Command\BanUserCommand;
use Talav\UserBundle\Message\Event\UserBannedEvent;

#[AsMessageHandler]
final class BanUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface  $userRepository,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public function __invoke(BanUserCommand $command): void
    {
        $this->userRepository->save(
            $command->user
                ->setIsBanned(true)
                ->setBannedAt(new \DateTimeImmutable())
        );
        $this->dispatcher->dispatch(new UserBannedEvent($command->user));
    }
}
