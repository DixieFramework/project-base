<?php

declare(strict_types=1);

namespace Talav\WebBundle\Repository;

use Talav\WebBundle\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Country|null find($id, $lockMode = null, $lockVersion = null)
 * @method Country|null findOneBy(array $criteria, array $orderBy = null)
 * @method Country[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    public function findAll(): array
    {
        $query = $this->getEntityManager()->createQuery('SELECT c FROM Talav\WebBundle\Entity\Country c');
        $query->enableResultCache();
        $query->useQueryCache(true);

        return $query->getResult();
    }

    public function findByIds(array $countryIds): array
    {
        return $this->createQueryBuilder('find_by_country_ids')
            ->select('c')
            ->from('Talav\WebBundle\Entity\Country ', 'c')
            ->where('c.id IN (:countryIds)')
            ->setParameter('countryIds', $countryIds)
            ->getQuery()
            ->useQueryCache(true)
            ->enableResultCache()
            ->getResult();
    }
}
