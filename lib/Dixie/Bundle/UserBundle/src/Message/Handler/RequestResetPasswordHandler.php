<?php

declare(strict_types=1);

namespace Talav\UserBundle\Message\Handler;

use Talav\Component\User\Model\UserInterface;
use Talav\Component\User\Repository\UserRepositoryInterface;
use Talav\UserBundle\Message\Command\RequestResetPasswordCommand;
use Talav\UserBundle\Entity\ResetPasswordToken;
use Talav\UserBundle\Message\Event\ResetPasswordRequestedEvent;
use Talav\Component\User\Exception\ResetPasswordOngoingException;
use Talav\UserBundle\Exception\UserNotFoundException;
use Talav\UserBundle\Repository\ResetPasswordTokenRepository;
use Talav\UserBundle\Repository\ResetPasswordTokenRepositoryInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RequestResetPasswordHandler
{
    private const EXPIRE_IN = 30;

    public function __construct(
        private readonly ResetPasswordTokenRepository $resetPasswordTokenRepository,
        private readonly UserRepositoryInterface               $userRepository,
        private readonly EventDispatcherInterface              $dispatcher
    ) {
    }

    public function __invoke(RequestResetPasswordCommand $command): void
    {
        $user = $this->findUserByEmail($command->email);
        $token = $this->findOngoingRequestTokenFor($user);

        if (null === $token) {
            $token = new ResetPasswordToken();
        }

        $token->setUser($user);
        $this->resetPasswordTokenRepository->save($token);
        $this->dispatcher->dispatch(new ResetPasswordRequestedEvent($user, $token));
    }

    private function findUserByEmail(?string $email): UserInterface
    {
        /** @var UserInterface|null $user */
        $user = $this->userRepository->findOneByEmail((string) $email);
        if (null === $user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    private function findOngoingRequestTokenFor(User $user): ?ResetPasswordToken
    {
        /** @var ResetPasswordToken|null $token */
        $token = $this->resetPasswordTokenRepository->findFor($user);
        if (null !== $token && ! $token->isExpired(self::EXPIRE_IN)) {
            throw new ResetPasswordOngoingException();
        }

        return $token;
    }
}
