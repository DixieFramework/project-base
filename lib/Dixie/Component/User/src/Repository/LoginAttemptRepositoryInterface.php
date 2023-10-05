<?php

declare(strict_types=1);

namespace Talav\Component\User\Repository;

use Talav\Component\Resource\Repository\RepositoryInterface;
use Talav\Component\User\Model\UserInterface;

interface LoginAttemptRepositoryInterface extends RepositoryInterface
{
    public function deleteAttemptsFor(UserInterface $user): void;

    public function countRecentFor(UserInterface $user): int;
}
