<?php

declare(strict_types=1);

namespace Talav\Component\User\Model;

use Symfony\Component\Security\Core\Role\Role;

interface RolesInterface
{
	/**
	 * Check if the role exist.
	 *
	 * @param string $role The role name
	 *
	 * @return bool
	 */
	public function hasRole($role);

	/**
	 * Set the roles.
	 *
	 * This overwrites any previous roles.
	 *
	 * @param string[] $roles The roles
	 *
	 * @return self
	 */
	public function setRoles(array $roles);

	/**
	 * Add a role.
	 *
	 * @param string $role The role name
	 *
	 * @return self
	 */
	public function addRole($role);

	/**
	 * Remove a role.
	 *
	 * @param string $role The role name
	 *
	 * @return self
	 */
	public function removeRole($role);

	/**
	 * Get the roles.
	 *
	 * @return Role[]|string[] The user roles
	 */
	public function getRoles();
}
