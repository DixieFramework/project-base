<?php

declare(strict_types=1);

namespace Talav\Component\User\Repository;

use Talav\Component\Resource\Repository\ResourceRepository;
use Talav\Component\User\Model\UserInterface;

final class LoginAttemptRepository extends ResourceRepository implements LoginAttemptRepositoryInterface
{
    public function countRecentFor(UserInterface $user): int
    {
        return $this->count([
            'owner' => $user,
        ]);
    }

    public function deleteAttemptsFor(UserInterface $user): void
    {
        $this->createQueryBuilder('a')
            ->where('a.owner = :user')
            ->setParameter('user', $user)
            ->delete()
            ->getQuery()
            ->execute();
    }
}
