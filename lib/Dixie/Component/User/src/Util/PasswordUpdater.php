<?php

declare(strict_types=1);

namespace Talav\Component\User\Util;

use Talav\Component\User\Model\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class PasswordUpdater implements PasswordUpdaterInterface
{
    private $hasherFactory;

    public function __construct(PasswordHasherFactoryInterface $hasherFactory)
    {
        $this->hasherFactory = $hasherFactory;
    }

    public function hashPassword(UserInterface $user)
    {
        $plainPassword = $user->getPlainPassword();

        if ('' === $plainPassword || null === $plainPassword) {
            return;
        }

        $hasher = $this->hasherFactory->getPasswordHasher($user);
        $hashedPassword = $hasher->hash($plainPassword);
        $user->setPassword($hashedPassword);
        $user->eraseCredentials();
    }
}
