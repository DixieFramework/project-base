<?php

declare(strict_types=1);

namespace Talav\UserBundle\Repository;

use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\Repository\ResetPasswordRequestRepositoryTrait;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\UserBundle\Entity\ResetPasswordRequest;

class ResetPasswordRequestRepository extends EntityRepository implements ResetPasswordRequestRepositoryInterface
{
    use ResetPasswordRequestRepositoryTrait;

    public function createResetPasswordRequest(object $user, DateTimeInterface $expiresAt, string $selector, string $hashedToken): ResetPasswordRequestInterface
    {
        if (!$user instanceof UserInterface) {
            throw new RuntimeException('Unable to pass an user instance');
        }

        return new ResetPasswordRequest($user, $expiresAt, $selector, $hashedToken);
    }
}
