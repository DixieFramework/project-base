<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Repository;

use Talav\Component\Resource\Repository\RepositoryInterface;
use Talav\CoreBundle\Entity\Property;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for property entity.
 *
 * @template-extends AbstractRepository<Property>
 */
class PropertyRepository extends AbstractRepository implements RepositoryInterface
{
//    public function __construct(ManagerRegistry $registry)
//    {
//        parent::__construct($registry, Property::class);
//    }

    /**
     * Gets a property for the given name.
     */
    public function findOneByName(string $name): ?Property
    {
        return $this->findOneBy(['name' => $name]);
    }
}
