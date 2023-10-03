<?php

declare(strict_types=1);

namespace Talav\UserBundle\EventListener;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Talav\Component\User\Manager\UserManagerInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\Component\User\Repository\UserRepositoryInterface;

/**
 * Defines the method that 'listens' to the 'kernel.controller' event, which is
 * triggered whenever a controller is executed in the application.
 */
#[AsEventListener(event: KernelEvents::CONTROLLER, method: 'registerCurrentController')]
class UserControllerSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security             $security,
        private UserManagerInterface $userManager
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'registerCurrentController',
        ];
    }

    public function registerCurrentController(ControllerEvent $event): void
    {
        // this check is needed because in Symfony a request can perform any
        // number of sub-requests. See
        // https://symfony.com/doc/current/components/http_kernel.html#sub-requests
        if ($event->isMainRequest()) {
            /** @var UserInterface $user */
            $user = $this->security->getUser();

            $controller = $event->getController();
//            $this->twigExtension->setController($controller);

//            dump($controller);

//            $this->logService->log($event->getRequest(), $user);

            if (gettype($controller)== "array" && $user && $controller[1] !== 'chatUpdate' && $controller[1] !== 'chatDiscussionUpdate') {
                try {
                    $user->setLastActivityAt(new DateTimeImmutable('now', new DateTimeZone('Europe/Paris')));
                } catch (Exception) {
                    $user->setLastActivityAt(new DateTimeImmutable());
                }
                // If the user is logged in from two browsers and logs out from one of them,
                // the last logout date field is no longer null. You have to set the logout date to null.
                if ($user->getLastLogout()) {
                    $user->setLastLogout(null);
                }

                $this->userManager->update($user, false);
            }
            $this->userManager->flush();
        }
    }
}
