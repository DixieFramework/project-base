<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Traits;

use Doctrine\Common\Collections\Collection;
use Talav\PermissionBundle\Entity\Permission;

trait HasPermissions
{
    public function hasPermissionTo(Permission $permission): bool
    {
        return $this->getPermissions()->contains($permission) || $this->hasPermissionViaRole($permission);
    }

    public function hasPermissionViaRole(Permission $permission): bool
    {
        return $this->hasRole($permission->getRoles()->toArray());
    }

    private function findPermissionByName(Collection $permissions, string $name): Permission|null
    {
        foreach ($permissions->toArray() as $permission)
            if (strcasecmp($permission->getName(), $name) === 0)
                return $permission;
        return null;
    }
}
