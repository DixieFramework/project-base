<?php

declare(strict_types=1);

namespace Talav\Component\User\Manager;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\RememberMe\RememberMeHandlerInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

class LoginManager implements LoginManagerInterface
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly UserCheckerInterface $userChecker,
        private readonly SessionAuthenticationStrategyInterface $sessionStrategy,
        private readonly RequestStack $requestStack,
        private readonly ?RememberMeHandlerInterface $rememberMeService = null
    ) {
    }

    final public function logInUser(string $firewallName, UserInterface $user, Response $response = null): void
    {
        $this->userChecker->checkPreAuth($user);
//	    try {
//		    $this->userChecker->checkPreAuth($user);
//		    $this->userChecker->checkPostAuth($user);
//	    } catch (AccountStatusException $e) {
//			dd($e);
//		    return;
//	    }

        $token = $this->createToken($firewallName, $user);
        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request) {
            $this->sessionStrategy->onAuthentication($request, $token);

            if (null !== $response && null !== $this->rememberMeService) {
                $this->rememberMeService->createRememberMeCookie($user);
            }
        }

        $this->tokenStorage->setToken($token);
    }

    protected function createToken(string $firewall, UserInterface $user): UsernamePasswordToken
    {
        return new UsernamePasswordToken($user, $firewall, $user->getRoles());
    }
}
