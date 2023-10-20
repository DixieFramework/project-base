<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Talav\Component\Resource\Repository\RepositoryInterface;
use Talav\Component\Resource\Repository\RepositoryPaginatorTrait;
use Talav\Component\User\Model\UserInterface;
use Talav\ProfileBundle\Entity\UserFriend;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

class UserFriendRepository extends EntityRepository implements RepositoryInterface
{
    use RepositoryPaginatorTrait;

    public function getFriendsQuery(UserInterface $user): Query
    {
        return $this->createQueryBuilder('f')
            ->where('f.user = :user')
            ->setParameter('user', $user)
            ->innerJoin('f.friend', 'u', Join::WITH, 'u.enabled = 1')
            ->orderBy('u.lastActivityAt', 'DESC')
            ->addOrderBy('u.username', 'ASC')
            ->getQuery();
    }

    public function getFriendsCount(UserInterface $user, \DateTime $lastActivity = null): int
    {
        $queryBuilder = $this->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->where('f.user = :user')
            ->setParameter('user', $user)
            ->innerJoin('f.friend', 'u', Join::WITH, 'u.enabled = 1');

        if ($lastActivity instanceof \DateTime) {
            $queryBuilder->andWhere('u.lastActivityAt > :lastActivity');
            $queryBuilder->setParameter('lastActivity', $lastActivity);
        }

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function getFriend(UserInterface $user, UserInterface $friend): ?UserFriend
    {
        return $this->findOneBy([
            'user' => $user,
            'friend' => $friend,
        ]);
    }
}
