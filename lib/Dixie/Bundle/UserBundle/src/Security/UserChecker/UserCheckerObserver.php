<?php

declare(strict_types=1);

namespace Talav\UserBundle\Security\UserChecker;

use Symfony\Component\Security\Core\User\UserCheckerInterface;

final class UserCheckerObserver implements UserCheckerInterface
{
    /**
     * @var UserCheckerInterface[]
     */
    private array $checkers = [];

    public function add(UserCheckerInterface $userChecker)
    {
        $this->checkers[] = $userChecker;
    }

    public function checkPreAuth(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        foreach ($this->checkers as $checker) {
            $checker->checkPreAuth($user);
        }
    }

    public function checkPostAuth(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        foreach ($this->checkers as $checker) {
            $checker->checkPostAuth($user);
        }
    }
}
