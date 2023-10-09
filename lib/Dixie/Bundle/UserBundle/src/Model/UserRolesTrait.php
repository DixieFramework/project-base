<?php

declare(strict_types=1);

namespace Talav\UserBundle\Model;

use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Talav\PermissionBundle\Entity\RoleInterface;

trait UserRolesTrait
{
    /** @var RoleInterface[] */

    //#[ORM\ManyToMany(targetEntity: 'Talav\PermissionBundle\Entity\Role')]
    protected Collection $userRoles;

    /**
     * Add userRoles.
     *
     * @return self
     */
    public function addUserRole(RoleInterface $userRoles): self
    {
        if (!$this->userRoles->contains($userRoles)) {
            $this->userRoles[] = $userRoles;
        }

        return $this;
    }

    /**
     * Remove userRoles.
     */
    public function removeUserRole(RoleInterface $userRoles): self
    {
        if ($this->userRoles->contains($userRoles)) {
            $this->userRoles->removeElement($userRoles);
        }

        return $this;
    }

    /**
     * Has userRole.
     *
     * @return bool
     */
    public function hasUserRole(RoleInterface|string $role): bool
    {
        if ($role instanceof RoleInterface) {
            $role = $role->getName();
        } else {
            throw new \InvalidArgumentException(
                sprintf('$role must be an instance of %s or a string', RoleInterface::class)
            );
        }

        return $this->userRoles->contains($role);
    }

    /**
     * Get userRoles.
     *
     * @return RoleInterface[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function findUserRole(string $role): ?RoleInterface
    {
//        dd(array_map(function ($role) { return $role->getName(); }, $this->userRoles->toArray()));

        /** @var RoleInterface $roleItem */
        foreach ($this->getUserRoles() as $roleItem) {
            if ($role == $roleItem->getName()) {
                return $roleItem;
            }
        }

        return null;
    }

    public function getRoleObjects(): iterable
    {
        $roles = [];
        foreach ($this->getUserRoles() as $userRole) {
            $roles[] = $userRole->getName();
        }

        return $roles;
    }
}
