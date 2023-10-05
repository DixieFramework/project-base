<?php

declare(strict_types=1);

namespace Talav\UserBundle\Security\UserChecker;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Talav\Component\User\Exception\TooManyLoginAttemptsException;
use Talav\Component\User\Model\UserInterface;
use Talav\UserBundle\Message\Event\LoginAttemptsLimitReachedEvent;
use Talav\UserBundle\Security\AuthenticationExceptionTrait;
use Talav\UserBundle\Service\LoginAttemptService;

final class LoginAttemptsUserChecker implements UserCheckerInterface
{
	use AuthenticationExceptionTrait;

	public function __construct(
		private LoginAttemptService $loginAttemptService,
		private EventDispatcherInterface $dispatcher
	) {
	}

    public function checkPreAuth(SymfonyUserInterface $user)
    {
	    if (!$user instanceof UserInterface) {
		    return;
	    }

	    if ($this->loginAttemptService->limitReachedFor($user)) {
		    if ($this->loginAttemptService->countRecentFor($user) === $this->loginAttemptService::ATTEMPTS) {
			    $this->dispatcher->dispatch(new LoginAttemptsLimitReachedEvent($user));
		    }

		    $this->throwException(new TooManyLoginAttemptsException());
	    }
    }

    public function checkPostAuth(SymfonyUserInterface $user)
    {
    }
}
