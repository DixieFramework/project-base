<?php

declare(strict_types=1);

namespace Talav\UserBundle\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Talav\Component\Resource\Manager\ManagerInterface;
use Talav\Component\User\Manager\UserManagerInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\Component\User\Util\TokenGeneratorInterface;
use Talav\UserBundle\Event\TalavUserEvents;
use Talav\UserBundle\Event\UserEvent;
use Talav\UserBundle\Mailer\UserMailer;
use Talav\UserBundle\Mailer\UserMailerInterface;

final class WelcomeEmailSubscriber implements EventSubscriberInterface
{
//    private UserManagerInterface $userManager;
//
//    private UserMailerInterface $mailer;
//
//    public function __construct(UserManagerInterface $userManager, UserMailerInterface $mailer)
//    {
//        $this->userManager = $userManager;
//        $this->mailer = $mailer;
//    }

    public function __construct(
        private readonly UserMailer $mailer,
        private readonly TokenGeneratorInterface $tokenGenerator,
        private readonly UrlGeneratorInterface $router,
        private readonly RequestStack $requestStack,
        private readonly ManagerInterface $roleManager
    ) { }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            TalavUserEvents::REGISTRATION_COMPLETED => 'onRegistrationComplete',
        ];
    }

    public function onRegistrationComplete(UserEvent $event): void
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
//        $user = $event->getUser();
//        $supportedClass = $this->userManager->getClassName();
//        if (!$user instanceof $supportedClass) {
//            return;
//        }
//        if (!$user instanceof UserInterface) {
//            throw new \UnexpectedValueException('In order to use this subscriber, your class has to implement UserInterface');
//        }
//        $this->mailer->sendRegistrationSuccessfulEmail($user);
    }
}
