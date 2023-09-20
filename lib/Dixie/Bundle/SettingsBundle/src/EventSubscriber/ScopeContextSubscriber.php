<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\EventSubscriber;

use Talav\SettingsBundle\Context\ScopeContextInterface;
use Talav\SettingsBundle\Model\SettingsOwnerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ScopeContextSubscriber implements EventSubscriberInterface
{
    protected $scopeContext;

    protected $securityTokenStorage;

    public function __construct(ScopeContextInterface $scopeContext, TokenStorageInterface $tokenStorage)
    {
        $this->scopeContext = $scopeContext;
        $this->securityTokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onRequest',
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $token = $this->securityTokenStorage->getToken();
        if ($token instanceof TokenInterface) {
            $user = $token->getUser();
            if ($user instanceof UserInterface && $user instanceof SettingsOwnerInterface) {
                $this->scopeContext->setScopeOwner(ScopeContextInterface::SCOPE_USER, $user);
            }
        }
    }
}
