<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Talav\ProfileBundle\Entity\AttributeCategory;

/**
 * @method AttributeCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method AttributeCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method AttributeCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributeCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttributeCategory::class);
    }

    public function findByName(string $name): ?AttributeCategory
    {
        $query = $this->getEntityManager()->createQuery('SELECT c FROM DatingLibre\AppBundle\Entity\Category c
        WHERE c.name = :name');

        $query->useQueryCache(true);
        $query->enableResultCache();
        $query->setParameter('name', $name);

        return $query->getOneOrNullResult();
    }

    public function findAll(): array
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT c FROM DatingLibre\AppBundle\Entity\Category c');

        $query->enableResultCache();
        $query->useQueryCache(true);

        return $query->getResult();
    }
}
