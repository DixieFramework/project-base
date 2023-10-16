<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Repository;

use Doctrine\ORM\Query\ResultSetMapping;
use Talav\Component\User\Model\UserInterface;
use Talav\ProfileBundle\Entity\Suspension;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Suspension|null find($id, $lockMode = null, $lockVersion = null)
 * @method Suspension|null findOneBy(array $criteria, array $orderBy = null)
 * @method Suspension[]    findAll()
 * @method Suspension[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class SuspensionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Suspension::class);
    }

    public function closeAllByUserId(UserInterface $moderatorId, UserInterface $userId): void
    {
        $sql =<<<EOD
UPDATE datinglibre.suspensions
    SET status = :closed,
        user_closed_id = :userClosedId,
        updated_at = now()
    WHERE user_id = :userId
    AND status = :open
EOD;
        $query = $this->getEntityManager()->createNativeQuery($sql, new ResultSetMapping());
        $query->setParameter('userId', $userId);
        $query->setParameter('userClosedId', $moderatorId);
        $query->setParameter('closed', Suspension::CLOSED);
        $query->setParameter('open', Suspension::OPEN);
        $query->execute();
    }
}
