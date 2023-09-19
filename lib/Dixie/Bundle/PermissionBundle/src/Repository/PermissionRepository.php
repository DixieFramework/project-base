<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Talav\Component\Resource\Repository\ResourceRepository;
use Talav\PermissionBundle\Entity\Permission;
use Talav\PermissionBundle\Entity\PermissionInterface;
use Talav\PermissionBundle\Entity\RoleInterface;

class PermissionRepository extends ResourceRepository implements PermissionRepositoryInterface
{
	/**
	 * @return PermissionInterface[]
	 */
	public function findAll()
	{
		return parent::findAll();
	}

	public function findRolePermission(RoleInterface $role, string $permission): ?PermissionInterface
	{
		return $this->findOneBy(['role_id' => $role->getId(), 'permission' => $permission]);
	}

	/**
	 * @return array<array<string, string|bool>>
	 */
	public function getAllAsArray(): array
	{
		$qb = $this->createQueryBuilder('rp');

		$qb->select('r.name as role,rp.permission')
			->leftJoin('rp.role', 'r');

		return $qb->getQuery()->getArrayResult(); // @phpstan-ignore-line
	}
}
