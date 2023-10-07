<?php

declare(strict_types=1);

namespace Talav\UserBundle\EventListener;

use Talav\Component\User\Model\UserInterface;
use Talav\UserBundle\Event\FilterUserResponseEvent;
use Talav\UserBundle\Event\TalavUserEvents;
use Talav\UserBundle\Event\UserEvent;
use Talav\UserBundle\Event\UserFormEvent;
use Talav\UserBundle\TalavUserBundle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ProfileChangeListener implements EventSubscriberInterface
{
    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            TalavUserEvents::PROFILE_EDIT_SUCCESS => 'profileEditSuccess',
            TalavUserEvents::PROFILE_EDIT_COMPLETED => 'profileEditCompleted',
        ];
    }

    public function profileEditCompleted(FilterUserResponseEvent $event, string $eventName, EventDispatcherInterface $eventDispatcher): void
    {
//        $eventDispatcher->dispatch(new UserEvent($event->getUser(), $event->getRequest()), TalavUserEvents::USER_LOCALE_CHANGED);
//        $eventDispatcher->dispatch(new UserEvent($event->getUser(), $event->getRequest()), TalavUserEvents::USER_TIMEZONE_CHANGED);
    }

    public function profileEditSuccess(UserFormEvent $event)
    {
        $user = $event->getUser();

        if ($this->isProfileCompleted($user)) {
            $user->setProfileCompleted(true);
        }
    }

    private function isProfileCompleted(UserInterface $user): bool
    {
        return $user->getProfile()->getFirstName() !== null &&
            $user->getProfile()->getLastName() !== null &&
            $user->getProfile()->getBirthDate() !== null &&
            $user->getProfile()->getGender() !== null;
    }
}
