<?php

declare(strict_types=1);

namespace Talav\WebBundle\Repository;

use Talav\WebBundle\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    public function findByRegion(mixed $regionId): array
    {
        $query = $this->getEntityManager()->createQuery('SELECT c FROM Talav\WebBundle\Entity\City c
        WHERE c.region = :regionId
        ORDER BY c.name ASC');

        $query->enableResultCache();
        $query->useQueryCache(true);
        $query->setParameter('regionId', $regionId);

        return $query->getResult();
    }
}
