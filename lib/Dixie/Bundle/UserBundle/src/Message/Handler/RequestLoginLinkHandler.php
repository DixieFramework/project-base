<?php

declare(strict_types=1);

namespace Talav\UserBundle\Message\Handler;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Talav\Component\User\Repository\UserRepositoryInterface;
use Talav\UserBundle\Message\Command\RequestLoginLinkCommand;
use Talav\UserBundle\Message\Event\LoginLinkRequestedEvent;

/**
 * Class RequestLoginLinkHandler.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
#[AsMessageHandler]
final class RequestLoginLinkHandler
{
    public function __construct(
        private readonly LoginLinkHandlerInterface $loginLinkHandler,
        private readonly UserRepositoryInterface   $userRepository,
        private readonly EventDispatcherInterface  $dispatcher
    ) {
    }

    public function __invoke(RequestLoginLinkCommand $command): void
    {
        $user = $this->userRepository->findOneByEmail((string) $command->email);
        if (null === $user) {
            throw new \InvalidArgumentException();//\UserNotFoundException();
        }

        $loginLinkDetails = $this->loginLinkHandler->createLoginLink($user);
        $this->dispatcher->dispatch(new LoginLinkRequestedEvent($user, $loginLinkDetails));
    }
}
