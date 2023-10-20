<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Repository;

use Doctrine\ORM\Query\Expr\Join;
use Talav\ProfileBundle\Entity\UserInterestFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserInterestFilter|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserInterestFilter|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserInterestFilter[]    findAll()
 * @method UserInterestFilter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserInterestFilterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInterestFilter::class);
    }

    public function findInterestFiltersByProfileId(mixed $profileId): array
    {
        return $this->createQueryBuilder('uif')
            ->select('i.id, i.name')
            ->innerJoin('uif.interest', 'i', Join::WITH, 'i.id = uif.interest')
            ->where('uif.profile = :profile')
            ->setParameter('profile', $profileId)
            ->getQuery()
            ->getResult();


        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('Talav\ProfileBundle\Entity\Interest', 'interest');
        $rsm->addFieldResult('interest', 'id', 'id');
        $rsm->addFieldResult('interest', 'name', 'name');

        $query = $this->getEntityManager()
            ->createNativeQuery(<<<EOD
SELECT i.id, i.name FROM user_interest_filter uif
INNER JOIN interest i ON i.id = uif.interest_id
WHERE uif.profile_id = :profileId
EOD, $rsm);

        $query->setParameter(':profileId', $profileId);

        return $query->getResult();
    }

    public function deleteByUserId(mixed $userId): void
    {
        $query = $this->getEntityManager()
            ->createQuery('DELETE FROM Talav\ProfileBundle\Entity\UserInterestFilter uif WHERE uif.profile = :userId')
//            ->createNativeQuery(<<<EOD
//DELETE FROM Talav\ProfileBundle\Entity\UserInterestFilter uif
//WHERE uif.profile_id = :userId
//EOD, new ResultSetMapping());

        ->setParameter(':userId', $userId)
        ->execute();
    }
}
