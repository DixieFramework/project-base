<?php

declare(strict_types=1);

namespace Talav\UserBundle\Message\Handler;

use Application\Authentication\Command\UnbanUserCommand;
use Domain\Authentication\Event\UserUnbannedEvent;
use Domain\Authentication\Repository\UserRepositoryInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UnbanUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public function __invoke(UnbanUserCommand $command): void
    {
        $this->repository->save(
            $command->user
                ->setIsBanned(false)
                ->setBannedAt(null)
        );
        $this->dispatcher->dispatch(new UserUnbannedEvent($command->user));
    }
}
