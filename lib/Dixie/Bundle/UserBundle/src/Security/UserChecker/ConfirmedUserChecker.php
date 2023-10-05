<?php

declare(strict_types=1);

namespace Talav\UserBundle\Security\UserChecker;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Talav\Component\User\Model\UserInterface;

final class ConfirmedUserChecker implements UserCheckerInterface
{
    public function checkPreAuth(SymfonyUserInterface $user)
    {
    }

    public function checkPostAuth(SymfonyUserInterface $user)
    {
        if (!$user instanceof UserInterface) {
            return;
        }

        if (!$user->isVerified()) {
            throw new CustomUserMessageAccountStatusException('You need to confirm your account');
        }
    }
}
