<?php

declare(strict_types=1);

namespace Talav\UserBundle\Security\UserChecker;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Talav\Component\User\Exception\TooManyLoginAttemptsException;
use Talav\Component\User\Exception\UserBannedException;
use Talav\Component\User\Model\UserInterface;
use Talav\ProfileBundle\Entity\Suspension;
use Talav\UserBundle\Entity\User;
use Talav\UserBundle\Exception\UserSuspendedException;
use Talav\UserBundle\Message\Event\LoginAttemptsLimitReachedEvent;
use Talav\UserBundle\Security\AuthenticationExceptionTrait;
use Talav\UserBundle\Service\LoginAttemptService;

final class SuspendedUserChecker implements UserCheckerInterface
{
	use AuthenticationExceptionTrait;

	public function __construct(
		private EventDispatcherInterface $dispatcher
	) {
	}

    public function checkPreAuth(SymfonyUserInterface $user)
    {
        if (!$user instanceof UserInterface) {
            return;
        }

        if ($user->getProfile()->isSuspended()) {
            /** @var Collection<int, Suspension> $suspensions */
            $suspensions = $user->getProfile()->getSuspensions();
            /** @var Suspension $suspension */
            $suspension = $suspensions->last();

//            $ex = new LockedException('User account is locked.');
//            $ex->setUser($user);
//
//            throw $ex;

            $this->throwException(UserSuspendedException::create($user, $suspension));
            throw UserSuspendedException::create($user, $suspension);
        }
    }

    public function checkPostAuth(SymfonyUserInterface|User $user)
    {
    }
}
