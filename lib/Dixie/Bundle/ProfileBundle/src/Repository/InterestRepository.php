<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Repository;

use Talav\ProfileBundle\Entity\Interest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Interest|null find($id, $lockMode = null, $lockVersion = null)
 * @method Interest|null findOneBy(array $criteria, array $orderBy = null)
 * @method Interest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Interest::class);
    }

    public function findAll(): array
    {
        $query = $this->getEntityManager()->createQuery('SELECT i FROM DatingLibre\AppBundle\Entity\Interest i');
        $query->enableResultCache();
        $query->useQueryCache(true);
        return $query->getResult();
    }
}
