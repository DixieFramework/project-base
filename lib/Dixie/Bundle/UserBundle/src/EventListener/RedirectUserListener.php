<?php

declare(strict_types=1);

namespace Talav\UserBundle\EventListener;

use Talav\CoreBundle\Controller\AbstractController;
use Talav\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RedirectUserListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var RouterInterface
     */
    private $routerInterface;

    /**
     * RedirectUserListener constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param RouterInterface $routerInterface
     */
    public function __construct(TokenStorageInterface $tokenStorage, RouterInterface $routerInterface)
    {
        $this->tokenStorage = $tokenStorage;
        $this->routerInterface = $routerInterface;
    }

    /**
     * @param RequestEvent $responseEvent
     */
    public function onKernelRequest(RequestEvent $responseEvent): void
    {
        if ($this->isUserLogged() && $responseEvent->isMainRequest()) {
            $currentPath = $responseEvent->getRequest()->attributes->get('_route');
            if ($this->isOnAnonymousPage($currentPath)){
                $response = new RedirectResponse($this->routerInterface->generate(AbstractController::HOME_PAGE));
                $responseEvent->setResponse($response);
            }
        }
    }

    /**
     * @return bool
     */
    private function isUserLogged(): bool
    {
        if (!$this->tokenStorage->getToken()){
            return false;
        }
        return $this->tokenStorage->getToken()->getUser() instanceof UserInterface;
    }

    /**
     * @param string $currentPath
     * @return bool
     */
    private function isOnAnonymousPage(string $currentPath): bool
    {
        return in_array(
            $currentPath,
            [
                'talav_user_login',
                'talav_user_register',
                'user_verify',
                'app_check_email',
                'app_forgot_password_request',
                'app_reset_password',
//                'talav_user_login',
//                'talav_user_register',
//                'app_registration_confirm_registration',
//                'app_registration_reset_password',
//                'app_registration_send_confirmation_email',
            ]
        );
    }

}
