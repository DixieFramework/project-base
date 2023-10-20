<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Repository;

use Talav\ProfileBundle\Entity\UserInterest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserInterest|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserInterest|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserInterest[]    findAll()
 * @method UserInterest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserInterestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInterest::class);
    }

    public function findInterestsByProfileId(mixed $profileId): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('Talav\ProfileBundle\Entity\Interest', 'interest');
        $rsm->addFieldResult('interest', 'id', 'id');
        $rsm->addFieldResult('interest', 'name', 'name');

        $query = $this->getEntityManager()
            ->createNativeQuery(<<<EOD
SELECT i.id, i.name FROM user_interest ui 
INNER JOIN interest i ON i.id = ui.interest_id 
WHERE ui.profile_id = :profileId
EOD, $rsm);

        $query->setParameter(':profileId', $profileId);

        return $query->getResult();
    }

    public function deleteByUserId(Uuid $userId): void
    {
        $query = $this->getEntityManager()
            ->createNativeQuery(<<<EOD
DELETE FROM datinglibre.user_interests ui WHERE ui.user_id = :userId 
EOD, new ResultSetMapping());

        $query->setParameter('userId', $userId);
        $query->execute();
    }
}
