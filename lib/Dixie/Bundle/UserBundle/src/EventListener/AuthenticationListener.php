<?php

declare(strict_types=1);

namespace Talav\UserBundle\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Talav\Component\Resource\Exception\SafeMessageException;
use Talav\Component\User\Manager\LoginManager;
use Talav\UserBundle\Event\FilterUserResponseEvent;
use Talav\UserBundle\Event\TalavUserEvents;
use Talav\UserBundle\Event\UserEvent;

#[AsEventListener(event: TalavUserEvents::REGISTRATION_COMPLETED, method: 'authenticate')]
#[AsEventListener(event: TalavUserEvents::REGISTRATION_CONFIRMED, method: 'authenticate')]
final class AuthenticationListener implements EventSubscriberInterface
{
    private LoginManager $loginManager;

    private string $firewallName;

    public function __construct(LoginManager $loginManager, string $firewallName)
    {
        $this->loginManager = $loginManager;
        $this->firewallName = $firewallName;
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            TalavUserEvents::REGISTRATION_COMPLETED => 'authenticate',
            TalavUserEvents::REGISTRATION_CONFIRMED => 'authenticate',
        ];
    }

    public function authenticate(FilterUserResponseEvent $event, string $eventName, EventDispatcherInterface $eventDispatcher): void
    {
        try {
            $this->loginManager->logInUser($this->firewallName, $event->getUser(), $event->getResponse());

            $eventDispatcher->dispatch(
                new UserEvent($event->getUser(), $event->getRequest()),
                TalavUserEvents::SECURITY_IMPLICIT_LOGIN
            );
        } catch (SafeMessageException) {
        //} catch (AccountStatusException) {
            // We simply do not authenticate users which do not pass the user checker (not enabled, expired, etc.).
        }
    }
}
