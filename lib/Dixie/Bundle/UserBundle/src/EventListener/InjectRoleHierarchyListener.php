<?php

declare(strict_types=1);

namespace Talav\UserBundle\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Talav\UserBundle\Entity\User;

class InjectRoleHierarchyListener
{
    public function __construct(private ContainerInterface $container) {dd($this);}

    #[NoReturn] public function preLoad(LifecycleEventArgs $event)
    {
        $this->postLoad($event);
    }

    #[NoReturn] public function postLoad(LifecycleEventArgs $event)
    {dd($this);
        if ($event->getObject() instanceof User && User::$roleHierarchy === null) {
            User::$roleHierarchy = $this->container->get('security.role_hierarchy');
        }
    }
}
