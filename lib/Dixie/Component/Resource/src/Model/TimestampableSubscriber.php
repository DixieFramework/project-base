<?php

declare(strict_types=1);

namespace Talav\Component\Resource\Model;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

final class TimestampSubscriber implements EventSubscriberInterface
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::postUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();
        if (method_exists($object, 'setCreatedAt') && method_exists($object, 'getCreatedAt')) {
            if (null === $object->getCreatedAt()) {
                $object->setCreatedAt(new \DateTimeImmutable());
            }
        }
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();
        if (method_exists($object, 'setUpdatedAt') && method_exists($object, 'getUpdatedAt')) {
            if (null === $object->getUpdatedAt()) {
                $object->setUpdatedAt(new \DateTimeImmutable());
            }
        }
    }
}
