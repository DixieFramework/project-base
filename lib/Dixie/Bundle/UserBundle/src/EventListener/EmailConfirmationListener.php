<?php

declare(strict_types=1);

namespace Talav\UserBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\Component\User\Util\TokenGeneratorInterface;
use Talav\UserBundle\Event\UserFormEvent;
use Talav\UserBundle\Mailer\UserMailer;
use Talav\UserBundle\Event\TalavUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Talav\UserBundle\Message\Event\UserRegisteredEvent;

final class EmailConfirmationListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly UserMailer $mailer,
        private readonly TokenGeneratorInterface $tokenGenerator,
        private readonly UrlGeneratorInterface $router,
        private readonly RequestStack $requestStack,
        private readonly ManagerInterface $roleManager,
        private readonly EventDispatcherInterface $eventDispatcher
    ) { }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            TalavUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        ];
    }

    public function onRegistrationSuccess(UserFormEvent $event): void
    {
        $user = $event->getUser();
        $user->setEnabled(false);
        $user->setVerified(false);

        $role = $this->roleManager->getRepository()->findOneByName('ROLE_USER');
        $user->sync('userRoles', new ArrayCollection([$role]));

        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
        }

        $this->mailer->sendConfirmationEmailMessage($user);

        $this->requestStack->getSession()->set('talav_user_send_confirmation_email/email', $user->getEmail());

        $event->setResponse(
            new RedirectResponse($this->router->generate('talav_user_registration_check_email'))
        );
    }
}
