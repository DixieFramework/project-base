<?php

declare(strict_types=1);

namespace Talav\UserBundle\Security\UserChecker;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Talav\Component\User\Exception\TooManyLoginAttemptsException;
use Talav\Component\User\Exception\UserBannedException;
use Talav\Component\User\Model\UserInterface;
use Talav\UserBundle\Entity\User;
use Talav\UserBundle\Message\Event\LoginAttemptsLimitReachedEvent;
use Talav\UserBundle\Security\AuthenticationExceptionTrait;
use Talav\UserBundle\Service\LoginAttemptService;

final class BannedUserChecker implements UserCheckerInterface
{
	use AuthenticationExceptionTrait;

	public function __construct(
		private EventDispatcherInterface $dispatcher
	) {
	}

    public function checkPreAuth(SymfonyUserInterface $user)
    {
    }

    public function checkPostAuth(SymfonyUserInterface|User $user)
    {
        if ($user instanceof User && $user->isBanned()) {
            $this->throwException(new UserBannedException());
        }
    }
}
