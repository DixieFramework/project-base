<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Repository;

use Talav\Component\User\Model\UserInterface;
use Talav\ProfileBundle\Entity\UserRelation;
use Doctrine\ORM\EntityRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

/**
 * FamilyAndFriendRepository.
 */
class UserRelationRepository extends EntityRepository
{
    public function getRelationsCount(UserInterface $member): int
    {
        $qb = $this->createQueryBuilder('r');

        return
            (int) $qb
                ->select('count(r.id)')
                ->where('r.confirmed = :confirmed')
                ->andWhere(
                    $qb->expr()->eq('r.owner', ':member'),
                )
                ->setParameter(':member', $member)
                ->setParameter(':confirmed', 'Yes')
                ->orderBy('r.updated', 'ASC')
                ->getQuery()
                ->getSingleScalarResult()
            ;
    }

    public function findRelationsFor(UserInterface $member)
    {
        $qb = $this->createQueryBuilder('r');

        return $qb
            ->where('r.confirmed = :confirmed')
            ->andWhere(
                $qb->expr()
                    ->orX(
                        $qb->expr()->eq('r.owner', ':member'),
                        $qb->expr()->eq('r.receiver', ':member'),
                    )
            )
            ->setParameter(':member', $member)
            ->setParameter(':confirmed', 'Yes')
            ->orderBy('r.updated', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findRelationBetween(UserInterface $owner, UserInterface $receiver): ?UserRelation
    {
        return
            $this
                ->createQueryBuilder('r')
                ->where('r.owner = :member')
                ->andWhere('r.receiver = :receiver')
                ->setParameter(':member', $owner)
                ->setParameter(':receiver', $receiver)
                ->getQuery()
                ->getOneOrNullResult()
            ;
    }

    public function findUnconfirmedRelationBetween(UserInterface $owner, UserInterface $receiver): ?UserRelation
    {
        return
            $this
                ->createQueryBuilder('r')
                ->where('r.owner = :member')
                ->andWhere('r.receiver = :receiver')
                ->andWhere('r.confirmed = :confirmed')
                ->setParameter(':member', $owner)
                ->setParameter(':receiver', $receiver)
                ->setParameter(':confirmed', 'No')
                ->getQuery()
                ->getOneOrNullResult()
            ;
    }

    public function getRelations(UserInterface $member, int $page, int $itemsPerPage): Pagerfanta
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.receiver', 'm')
            ->where('r.confirmed = :confirmed')
            ->andWhere('r.owner = :member')
            ->setParameter('member', $member)
            ->setParameter(':confirmed', 'Yes')
            ->orderBy('m.username', 'ASC')
        ;

        $notes = new Pagerfanta(new QueryAdapter($qb->getQuery()));
        $notes->setMaxPerPage($itemsPerPage);
        $notes->setCurrentPage($page);

        return $notes;
    }
}
