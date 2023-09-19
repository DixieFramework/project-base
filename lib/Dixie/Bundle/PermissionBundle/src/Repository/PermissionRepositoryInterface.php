<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Repository;

use Talav\PermissionBundle\Entity\Permission;
use Doctrine\ORM\NonUniqueResultException;
use Talav\PermissionBundle\Entity\PermissionInterface;
use Talav\PermissionBundle\Entity\RoleInterface;
use Talav\Component\User\Model\UserInterface;

interface PermissionRepositoryInterface
{
	public function findRolePermission(RoleInterface $role, string $permission): ?PermissionInterface;

	public function findAll();
//
//	/**
//	 * @param Permission $permission
//	 * @return Permission
//	 */
//	public function save(Permission $permission): Permission;
//
//	/**
//	 * @param UserInterface $user
//	 * @param string $permissionName
//	 * @return bool
//	 * @throws NonUniqueResultException
//	 */
//	public function userHasPermission(UserInterface $user, string $permissionName): bool;
//
//	/**
//	 * @param string $name
//	 * @return Permission
//	 */
//	public function getPermissionByName(string $name): Permission;
}
