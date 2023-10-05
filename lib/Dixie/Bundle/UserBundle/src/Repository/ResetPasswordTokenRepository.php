<?php

declare(strict_types=1);

namespace Talav\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Talav\Component\Resource\Repository\RepositoryPaginatorTrait;
use Talav\Component\User\Model\UserInterface;
use Talav\UserBundle\Entity\ResetPasswordToken;
use Talav\UserBundle\Repository\ResetPasswordTokenRepositoryInterface;
use Talav\Component\Resource\Repository\ResourceRepository;

final class ResetPasswordTokenRepository extends EntityRepository
{
    use RepositoryPaginatorTrait;
    public function findFor(UserInterface $user): ?ResetPasswordToken
    {
        try {
            /** @var ResetPasswordToken|null $result */
            $result = $this->createQueryBuilder('r')
                ->where('r.user = :user')
                ->setParameter('user', $user)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            return $result;
        } catch (NonUniqueResultException) {
            return null;
        }
    }

    public function findOneByToken(string $token): ?ResetPasswordToken
    {
        try {
            /** @var ResetPasswordToken|null $result */
            $result = $this->createQueryBuilder('r')
                ->where('r.token = :token')
                ->setParameter('token', $token)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            return $result;
        } catch (NonUniqueResultException) {
            return null;
        }
    }

    public function clean(): int
    {
        /** @var int $result */
        $result = $this->createQueryBuilder('r')
            ->where('r.created_at < :date')
            ->setParameter('date', new \DateTimeImmutable('-3 month'))
            ->delete(ResetPasswordToken::class, 'r')
            ->getQuery()
            ->execute();

        return $result;
    }
}
