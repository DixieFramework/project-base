<?php

declare(strict_types=1);

namespace Talav\UserBundle\Message\Handler;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Talav\Component\User\Model\UserInterface;
use Talav\Component\User\Repository\UserRepositoryInterface;
use Talav\UserBundle\Message\Command\ConfirmRegistrationCommand;
use Talav\UserBundle\Message\Event\UserRegistrationConfirmedEvent;

#[AsMessageHandler]
final class ConfirmRegistrationHandler
{
    public function __construct(
        private readonly UserRepositoryInterface  $userRepository,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public function __invoke(ConfirmRegistrationCommand $command): void
    {
        /** @var UserInterface|null $user */
        $user = $this->userRepository->findOneByCaseInsensitive([
            'email_verification_token' => $command->token,
        ]);
        if (null === $user) {
            throw new InvalidRegistrationTokenException();
        }

        $user->setConfirmationToken(null)
            ->setVerified(true);
        $this->userRepository->save($user);
        $this->dispatcher->dispatch(new UserRegistrationConfirmedEvent($user));
    }
}
