<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Service\ContainerService;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Persistence\ObjectManager;
use LogicException;
use Talav\CoreBundle\Traits\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @internal
 */
trait ContainerServiceTrait
{
    use ContainerAwareTrait;

    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }

    public function getDoctrine(): Registry
    {
        if (!$this->container->has('doctrine')) {
            throw new LogicException('The DoctrineBundle is not registered in your application.');
        }
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->container->get('doctrine');
    }

    protected function getEm(): ObjectManager
    {
        return $this->getDoctrine()->getManager();
    }
}
