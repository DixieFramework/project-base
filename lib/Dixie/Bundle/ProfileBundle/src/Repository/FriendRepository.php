<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Repository;

use Talav\Component\Resource\Repository\ResourceRepository;
use Talav\Component\User\Model\UserInterface;
use Talav\ProfileBundle\Entity\Friend;

/**
 * @template-extends ResourceRepository<Friend>
 */
final class FriendRepository extends ResourceRepository
{
    protected ?string $entity = Friend::class;
    protected ?string $alias = 'f';

//    protected function findByCriteria(array $criteria, array $orderBy = []): array
//    {
//        if (false !== $sortByDate = $this->sortService->get('date')) {
//            $orderBy['f.createdAt'] = $this->getOrderType($sortByDate);
//        }
//
//        if (false !== $sortByAcceptFriendship = $this->sortService->get('acceptFriendship')) {
//            $orderBy['f.updatedAt'] = $this->getOrderType($sortByAcceptFriendship);
//        }
//
//        return parent::findByCriteria([], $orderBy);
//    }

    public function getFriend(UserInterface $user, UserInterface $friend): ?Friend
    {
        return $this->findOneBy([
            'user' => $user,
            'friend' => $friend
        ]);
    }

    public function findByUser(UserInterface $user): array
    {
        $qb = $this->getQueryBuilder();

        $qb->where($qb->expr()->orX(
            $qb->expr()->eq('f.user', ':user'),
            $qb->expr()->eq('f.friend', ':user')
        ));
        $qb->setParameter('user', $user);

        return $this->findByCriteria([]);
    }
}
