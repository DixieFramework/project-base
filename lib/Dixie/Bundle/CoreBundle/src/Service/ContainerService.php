<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Service;

use Talav\CoreBundle\Service\ContainerService\ContainerServiceTrait;
use LogicException;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class ContainerService implements ContainerInterface
{
    use ContainerServiceTrait;

    public function __construct(ContainerInterface $container = null)
    {
        if ($container !== null) {
            $this->container = $container;
        }
    }

    public function get($id, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE): ?object
    {
        return $this->container->get($id, $invalidBehavior);
    }

    public function set($id, $service)
    {
        throw new LogicException('Not available here');
    }

    public function has($id): bool
    {
        return $this->container->has($id);
    }

    public function initialized($id): bool
    {
        return $this->container->initialized($id);
    }

    public function getParameter($name): float|int|bool|array|string|null
    {
        return $this->container->getParameter($name);
    }

    public function hasParameter($name): bool
    {
        return $this->container->hasParameter($name);
    }

    public function setParameter($name, $value)
    {
        throw new LogicException('Not available here');
    }
}
