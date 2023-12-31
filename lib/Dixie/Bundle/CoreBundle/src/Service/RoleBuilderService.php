<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Service;

use Talav\CoreBundle\Entity\User;
use Talav\CoreBundle\Enums\EntityName;
use Talav\CoreBundle\Enums\EntityPermission;
use Talav\CoreBundle\Interfaces\RoleInterface;
use Talav\CoreBundle\Model\Role;
use Elao\Enum\FlagBag;
use Talav\PermissionBundle\Entity\Permission;
use Talav\PermissionBundle\Repository\PermissionRepositoryInterface;
use Talav\PermissionBundle\Repository\RoleRepositoryInterface;

/**
 * Service to build roles with default access rights.
 */
class RoleBuilderService
{
    public function __construct(private readonly RoleRepositoryInterface $roleRepository, private readonly PermissionRepositoryInterface $permissionRepository)
    {
    }

    /**
     * Gets a role with default access rights for the given user.
     */
    public function getRole(User $user): Role
    {
        if (!$user->isEnabled()) {
            return $this->getRoleDisabled();
        }
        if ($user->isSuperAdmin()) {
            return $this->getRoleSuperAdmin();
        }
        if ($user->isAdmin()) {
            return $this->getRoleAdmin();
        }

        return $this->getRoleUser();
    }

    /**
     * Gets the admin role ('ROLE_ADMIN') with default access rights.
     */
    public function getRoleAdmin(): Role
    {
        return $this->getRoleWithAll(RoleInterface::ROLE_ADMIN);
    }

    /**
     * Gets disabled role with the no access right.
     */
    public function getRoleDisabled(): Role
    {
        $role = new Role(RoleInterface::ROLE_USER);
        $role->setOverwrite(true);

        return $role;
    }

    /**
     * Gets the super admin role ('ROLE_SUPER_ADMIN') with default access rights.
     */
    public function getRoleSuperAdmin(): Role
    {
        return $this->getRoleWithAll(RoleInterface::ROLE_SUPER_ADMIN);
    }

    /**
     * Gets the user role ('ROLE_USER') with the default access rights.
     */
    public function getRoleUser(): Role
    {
        $all = $this->getAllPermissions();
        $none = $this->getNonePermissions();
        $default = $this->getDefaultPermissions();
        $role = new Role(RoleInterface::ROLE_USER);
        $role->EntityCalculation = $all;
        $role->EntityCalculationState = $default;
        $role->EntityCategory = $default;
        $role->EntityCustomer = $default;
        $role->EntityGlobalMargin = $default;
        $role->EntityGroup = $default;
        $role->EntityLog = $none;
        $role->EntityProduct = $default;
        $role->EntityTask = $default;
        $role->EntityUser = $none;

        return $role;
    }

    /**
     * @return FlagBag<EntityPermission>
     *
     * @psalm-suppress InvalidArgument
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     */
    private function getAllPermissions()//: FlagBag
    {
        return $this->permissionRepository->findAll();
        //return FlagBag::from(...EntityPermission::sorted());
    }

    /**
     * @return FlagBag<EntityPermission>
     *
     *  @psalm-suppress InvalidArgument
     *  @psalm-suppress InvalidReturnType
     *  @psalm-suppress InvalidReturnStatement
     */
    private function getDefaultPermissions(): FlagBag
    {
        return FlagBag::from(
            EntityPermission::LIST,
            EntityPermission::EXPORT,
            EntityPermission::SHOW
        );
    }

    /**
     * @return FlagBag<EntityPermission>
     */
    private function getNonePermissions(): FlagBag
    {
        return new FlagBag(EntityPermission::class, FlagBag::NONE);
    }

    private function getRoleWithAll(string $roleName): \Talav\PermissionBundle\Entity\RoleInterface
    {
        $role = new \Groshy\Entity\Role($roleName);
        $permissions = $this->getAllPermissions();
        foreach ($permissions as $permission) {
            $permEntity = new Permission();
            $permEntity->setName($permission->getName());
            $role->addPermission($permEntity);
        }
dd($role);
        return $role;
    }
}
