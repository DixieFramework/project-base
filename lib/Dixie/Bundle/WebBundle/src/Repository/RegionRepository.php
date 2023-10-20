<?php

declare(strict_types=1);

namespace Talav\WebBundle\Repository;

use Talav\WebBundle\Entity\Region;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Region|null findOneBy(array $criteria, array $orderBy = null)
 * @method Region[]    findAll()
 * @method Region[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Region::class);
    }

    public function find($id, $lockMode = null, $lockVersion = null): ?Region
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT r from Talav\WebBundle\Entity\Region r WHERE r.id = :id');

        $query->enableResultCache();
        $query->setParameter('id', $id);
        $query->useQueryCache(true);

        return $query->getOneOrNullResult();
    }

    public function findByCountry(mixed $countryId): array
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT r FROM Talav\WebBundle\Entity\Region r
            WHERE r.country = :countryId
            ORDER BY r.name ASC');

        $query->enableResultCache();
        $query->useQueryCache(true);

        $query->setParameter('countryId', $countryId);
        $query->enableResultCache();

        return $query->getResult();
    }

    public function findByCity(mixed $cityId): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('Talav\WebBundle\Entity\Region', 'r');
        $rsm->addFieldResult('r', 'id', 'id');
        $rsm->addFieldResult('r', 'name', 'name');
        $rsm->addFieldResult('r', 'country', 'country_id');

        $query = $this->getEntityManager()->createNativeQuery(<<<EOD
SELECT region.id AS id,
       region.name AS name,
       region.country_id AS country_id
FROM region region
WHERE region.country_id = (SELECT country.id FROM city city
    INNER JOIN region region ON city.region_id = region.id
    INNER JOIN country country ON country.id = region.country_id
    WHERE city.id = :cityId);
EOD, $rsm);
        $query->setParameter('cityId', $cityId);
        $query->enableResultCache();

        return $query->getResult();
    }
}
