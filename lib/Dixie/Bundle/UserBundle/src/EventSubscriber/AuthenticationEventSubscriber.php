<?php

declare(strict_types=1);

namespace Talav\UserBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Helper\MailerHelper;
use Talav\UserBundle\Message\Command\RegisterLoginAttemptCommand;
use Talav\UserBundle\Message\Command\RegisterLoginIpAddressCommand;
use Talav\UserBundle\Message\Event\BadPasswordSubmittedEvent;
use Talav\UserBundle\Message\Event\LoginAttemptsLimitReachedEvent;
use Talav\UserBundle\Message\Event\LoginLinkRequestedEvent;
use Talav\UserBundle\Message\Event\LoginWithAnotherIpAddressEvent;
use Talav\UserBundle\Message\Event\UserRegisteredEvent;

final class AuthenticationEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MailerHelper $mailer,
        private readonly MessageBusInterface $bus
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            InteractiveLoginEvent::class => 'onInteractiveLogin',
            BadPasswordSubmittedEvent::class => 'onBadPasswordSubmitted',
            LoginWithAnotherIpAddressEvent::class => 'onLoginWithAnotherIpAddress',
            LoginAttemptsLimitReachedEvent::class => 'onLoginAttemptsLimitReached',
            LoginLinkRequestedEvent::class => 'onLoginLinkRequested',
//            ResetPasswordConfirmedEvent::class => 'onResetPasswordConfirmed',
//            ResetPasswordRequestedEvent::class => 'onResetPasswordRequested',
//            DefaultPasswordCreatedEvent::class => 'onDefaultPasswordCreated',
//            PasswordUpdatedEvent::class => 'onPasswordUpdated',
            UserRegisteredEvent::class => 'onUserRegistered',
//            UserRegistrationConfirmedEvent::class => 'onUserRegistrationConfirmed',
//            UserEmailedEvent::class => 'onUserEmailed',
        ];
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        /** @var UserInterface $user */
        $user = $event->getAuthenticationToken()->getUser();
        $ip = (string) $event->getRequest()->getClientIp();
        $this->bus->dispatch(new RegisterLoginIpAddressCommand($user, $ip));
    }

    public function onBadPasswordSubmitted(BadPasswordSubmittedEvent $event): void
    {
        $this->bus->dispatch(new RegisterLoginAttemptCommand($event->user));
    }

    public function onLoginWithAnotherIpAddress(LoginWithAnotherIpAddressEvent $event): void
    {
        $this->mailer->sendNotificationEmail(
            $event,
            template: '@TalavUser/email/login_with_another_ip_address.mail.twig',
            subject: 'authentication.mails.subjects.login_with_another_ip_address',
            domain: 'TalavUserBundle'
        );
    }

//    public function onPasswordUpdated(PasswordUpdatedEvent $event): void
//    {
//        $this->mailer->sendNotificationEmail(
//            $event,
//            template: '@app/domain/authentication/mail/password_updated.mail.twig',
//            subject: 'authentication.mails.subjects.password_updated',
//            domain: 'authentication'
//        );
//    }
//
//    public function onDefaultPasswordCreated(DefaultPasswordCreatedEvent $event): void
//    {
//        $this->mailer->sendNotificationEmail(
//            $event,
//            template: '@app/domain/authentication/mail/default_password_created.mail.twig',
//            subject: 'authentication.mails.subjects.password_updated',
//            domain: 'authentication'
//        );
//    }
//
    public function onLoginAttemptsLimitReached(LoginAttemptsLimitReachedEvent $event): void
    {
        $this->mailer->sendNotificationEmail(
            $event,
            template: '@TalavUser/email/login_attempts_limit_reached.mail.twig',
            subject: 'authentication.mails.subjects.login_attempts_limit_reached',
            domain: 'TalavUserBundle'
        );
    }

    public function onLoginLinkRequested(LoginLinkRequestedEvent $event): void
    {
        $this->mailer->sendNotificationEmail(
            $event,
            template: '@TalavUser/email/login_link.mail.twig',
            subject: 'authentication.mails.subjects.login_link_requested',
            subject_parameters: [
                '%name%' => $event->user->getUsername(),
            ],
            domain: 'TalavUserBundle'
        );
    }
//
//    public function onResetPasswordConfirmed(ResetPasswordConfirmedEvent $event): void
//    {
//        $this->mailer->sendNotificationEmail(
//            $event,
//            template: '@app/domain/authentication/mail/reset_password_confirmed.mail.twig',
//            subject: 'authentication.mails.subjects.reset_password_confirmed',
//            subject_parameters: [
//                '%name%' => $event->user->getUsername(),
//            ],
//            domain: 'authentication'
//        );
//    }
//
//    public function onResetPasswordRequested(ResetPasswordRequestedEvent $event): void
//    {
//        $this->mailer->sendNotificationEmail(
//            $event,
//            template: '@app/domain/authentication/mail/reset_password_request.mail.twig',
//            subject: 'authentication.mails.subjects.reset_password_requested',
//            domain: 'authentication'
//        );
//    }
//
//    public function onUserEmailed(UserEmailedEvent $event): void
//    {
//        $this->mailer->sendNotificationEmail(
//            $event,
//            template: '@admin/domain/authentication/mail/admin_contact.mail.twig',
//            subject: $event->subject,
//            domain:  'authentication'
//        );
//    }
//
    public function onUserRegistered(UserRegisteredEvent $event): void
    {
        $this->mailer->sendNotificationEmail(
            $event,
            template: '@TalavUser/email/registration_confirmation.mail.twig',
            subject: 'authentication.mails.subjects.registration_confirmation',
            domain: 'authentication'
        );
    }
//
//    public function onUserRegistrationConfirmed(UserRegistrationConfirmedEvent $event): void
//    {
//        $this->mailer->sendNotificationEmail(
//            $event,
//            template: '@app/domain/authentication/mail/registration_confirmed.mail.twig',
//            subject: 'authentication.mails.subjects.registration_confirmed',
//            domain: 'authentication'
//        );
//    }
}
