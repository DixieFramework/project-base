<?php

declare(strict_types=1);

namespace Talav\UserBundle\Security\UserChecker;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Talav\Component\User\Exception\UserRegistrationNotConfirmedException;
use Talav\Component\User\Model\UserInterface;
use Talav\UserBundle\Security\AuthenticationExceptionTrait;

final class ConfirmedUserChecker implements UserCheckerInterface
{
    use AuthenticationExceptionTrait;

    public function checkPreAuth(SymfonyUserInterface $user)
    {
	    $this->verifyAccountEnabled($user);
    }

    public function checkPostAuth(SymfonyUserInterface $user)
    {
	    if (!$user instanceof UserInterface) {
		    return;
	    }

	    if (!$user->isVerified()) {
		    $this->throwException(new UserRegistrationNotConfirmedException('authentication.exceptions.user_registration_not_confirmed', [], Response::HTTP_UNAUTHORIZED));
	    }
    }

	private function verifyAccountEnabled(UserInterface $user): void
	{
		if (!$user->isEnabled() || !$user->isVerified()) {
			$ex = new DisabledException('User account is disabled.');
			$ex->setUser($user);

			throw $ex;
		}
	}
}
