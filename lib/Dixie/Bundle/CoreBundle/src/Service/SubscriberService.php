<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * SubscriberService
 *
 * To inject service in container
 *
 * @property ContainerInterface $container
 */
class SubscriberService
{
    private $container;

    /**
     * SubscriberService constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * To get service
     *
     * @param string $className
     * @return object
     */
    public function get(string $className)
    {
        return $this->container->get($className);
    }
}
