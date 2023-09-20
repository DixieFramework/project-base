<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Tests\Functional;

use Talav\SettingsBundle\Tests\Functional\Model\User;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProvider implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        return new User(1, 'publisher', 'testpass');
    }

    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return User::class === $class;
    }
}
