<?php

declare(strict_types=1);

namespace Talav\Component\User\Model;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Talav\PermissionBundle\Entity\RoleInterface;

trait RolesTrait
{
//    /** @var RoleInterface[] */
//    protected Collection $roles;

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        return $this->roles->map(fn (RoleInterface $role): string => $role->getName())->getValues();
    }

    /**
     * {@inheritdoc}
     */
    public function getRolesCollection(): Collection
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getRole($roleName)
    {
        /** @var RoleInterface $item */
        foreach ($this->roles as $item) {
            if ($roleName == $item->getName()) {
                return $item;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function _hasRole($role): bool
    {
        if ($role instanceof RoleInterface) {
            $roleName = $role->getName();
        } elseif (is_string($role)) {
            $roleName = $role;
        } else {
            throw new \InvalidArgumentException(
                sprintf('$role must be an instance of %s or a string', RoleInterface::class)
            );
        }

        return (bool) $this->getRole($roleName);
    }

    /**
     * {@inheritdoc}
     */
    public function _addRole(RoleInterface $role): self
    {
        if (!$this->hasRole($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function _removeRole($role): void
    {
        if ($role instanceof RoleInterface) {
            $roleObject = $role;
        } elseif (is_string($role)) {
            $roleObject = $this->getRole($role);
        } else {
            throw new \InvalidArgumentException(
                sprintf('$role must be an instance of %s or a string', RoleInterface::class)
            );
        }
        if ($roleObject) {
            $this->roles->removeElement($roleObject);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function _setRoles(iterable $roles): self
    {
        if (!$roles instanceof Collection && !is_array($roles)) {
            throw new \InvalidArgumentException(
                '$roles must be an instance of Doctrine\Common\Collections\Collection or an array'
            );
        }

        $this->roles->clear();

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRolesCollection(Collection $collection): self
    {
        if (!$collection instanceof Collection) {
            throw new \InvalidArgumentException(
                '$collection must be an instance of Doctrine\Common\Collections\Collection'
            );
        }
        $this->roles = $collection;

        return $this;
    }

    // ---

//    public function hasRole($role)
//    {
//        if ($this->findUserRole($role)) {
//            return true;
//        }
//
//        return false;
//    }
//
//    public function addRole($role)
//    {
//        if (!$role instanceof RoleInterface) {
//            throw new \Exception('addRole takes a Role object as the parameter');
//        }
//
//        if (!$this->hasRole($role->getName())) {
//            $this->roles[] = $role;
//        }
//
//        return $this;
//    }
//
//    public function removeRole($role)
//    {
//        $roleElement = $this->findUserRole($role);
//        if ($roleElement) {
//            $this->roles->removeElement($roleElement);
//        }
//    }
//
//    public function findUserRole($role)
//    {
//        /** @var RoleInterface $roleItem */
//        foreach ($this->getRoles() as $roleItem) {
//            if ($role == $roleItem->getName()) {
//                return $roleItem;
//            }
//        }
//
//        return;
//    }

    // ---

    /**
     * Return roles if they exist
     *
     * @param array $roles roles
     *
     * @return bool
     */
    public function hasRoles(array $roles): bool
    {
        return !$this->roles->isEmpty() && !empty(array_intersect($roles, array_map(function ($role) { return $role->getName(); }, $this->roles->toArray())));
    }
}
