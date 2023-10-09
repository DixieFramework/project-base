<?php

declare(strict_types=1);

namespace Talav\UserBundle\Model;

use Doctrine\Common\Collections\Collection;
use Talav\PermissionBundle\Entity\RoleInterface;

interface UserRolesInterface
{
    public function addUserRole(RoleInterface $userRoles): self;

    public function removeUserRole(RoleInterface $userRoles): self;

    public function hasUserRole(RoleInterface|string $role): bool;

    public function getUserRoles(): Collection;

    public function findUserRole(string $role): ?RoleInterface;

    public function getRoleObjects(): iterable;
}
