<?php

declare(strict_types=1);

namespace Talav\Component\User\Model;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\Role\Role;
use Talav\PermissionBundle\Entity\RoleInterface;

interface RolesInterface
{
    /**
     * Returns the true Collection of Roles.
     *
     * @return Collection
     */
    public function getRolesCollection(): Collection;

    /**
     * Pass a string, get the desired Role object or null
     *
     * @param  string $roleName Role name
     *
     * @return Role|null
     */
    public function getRole($roleName);

    /**
     * Never use this to check if this user has access to anything!
     * Use the AuthorizationChecker, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $authorizationChecker->isGranted('ROLE_USER');
     *
     * @param  Role|string $role
     *
     * @throws \InvalidArgumentException
     *
     * @return boolean
     */
    public function hasRole($role): bool;

    /**
     * Adds a Role to the Collection.
     *
     * @param  RoleInterface $role
     *
     * @return UserInterface
     */
	public function addRole(RoleInterface $role): self;

    /**
     * Remove the Role object from collection
     *
     * @param  RoleInterface|string $role
     *
     * @throws \InvalidArgumentException
     */
    public function removeRole($role): void;

    /**
     * Pass an array or Collection of Role objects and re-set roles collection with new Roles.
     * Type hinted array due to interface.
     *
     * @param  iterable $roles Array of Role objects
     *
     * @throws \InvalidArgumentException
     *
     * @return UserInterface
     */
    public function setRoles(iterable $roles): self;

    /**
     * Directly set the Collection of Roles.
     *
     * @param  Collection $collection
     *
     * @throws \InvalidArgumentException
     *
     * @return UserInterface
     */
    public function setRolesCollection(Collection $collection): self;

	function hasRoles(array $roles): bool;
}
