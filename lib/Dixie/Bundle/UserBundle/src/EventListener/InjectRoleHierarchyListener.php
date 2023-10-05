<?php

declare(strict_types=1);

namespace Talav\UserBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\UserBundle\Entity\User;

#[AsEntityListener(event: 'postLoad', entity: User::class)]
class InjectRoleHierarchyListener
{
    public function __construct(private ContainerInterface $container) { }

    #[NoReturn] public function postLoad($user, PostLoadEventArgs $event)
    {
        if ($user instanceof UserInterface && User::$roleHierarchy === null) {
            User::$roleHierarchy = $this->container->get('security.role_hierarchy');
        }
    }
}
