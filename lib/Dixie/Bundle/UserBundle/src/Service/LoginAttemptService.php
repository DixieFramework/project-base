<?php

declare(strict_types=1);

namespace Talav\UserBundle\Service;

use Talav\Component\User\Model\UserInterface;
use Talav\Component\User\Util\TokenGeneratorInterface;
use Talav\UserBundle\Entity\LoginAttempt;
use Talav\Component\User\Repository\LoginAttemptRepositoryInterface;
use Talav\Component\User\Repository\UserRepositoryInterface;

final class LoginAttemptService
{
    public const ATTEMPTS = 3;

    public function __construct(
        private readonly LoginAttemptRepositoryInterface $userLoginAttemptRepository,
        private readonly UserRepositoryInterface         $userRepository,
        private readonly TokenGeneratorInterface         $tokenGenerator
    ) {
    }

    public function addAttempt(UserInterface $user): void
    {
        $this->userLoginAttemptRepository->save(LoginAttempt::createFor($user));

        if (null === $user->getLoginAttemptsResetToken()) {
            $token = $this->tokenGenerator->generateToken();
            $user->setLoginAttemptsResetToken($token);
            $this->userRepository->save($user);
        }
    }

    public function deleteAttemptsFor(UserInterface $user): void
    {
        $this->userLoginAttemptRepository->deleteAttemptsFor($user);

        if (null !== $user->getLoginAttemptsResetToken()) {
            $user->setLoginAttemptsResetToken(null);
            $this->userRepository->save($user);
        }
    }

    public function limitReachedFor(UserInterface $user): bool
    {
        return $this->userLoginAttemptRepository->countRecentFor($user) >= self::ATTEMPTS;
    }

    public function countRecentFor(UserInterface $user): int
    {
        return $this->userLoginAttemptRepository->countRecentFor($user);
    }
}
