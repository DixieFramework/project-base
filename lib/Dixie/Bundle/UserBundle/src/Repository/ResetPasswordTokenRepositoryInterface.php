<?php

declare(strict_types=1);

namespace Talav\UserBundle\Repository;

use Talav\Component\Resource\Repository\RepositoryInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\UserBundle\Entity\ResetPasswordToken;

interface ResetPasswordTokenRepositoryInterface extends RepositoryInterface
{
    public function findFor(UserInterface $user): ?ResetPasswordToken;

    public function findOneByToken(string $token): ?ResetPasswordToken;
}
