<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Repository;

use Doctrine\ORM\Exception\ORMException;

interface RoleRepositoryInterface
{
	public function getByPermission(string $permission): array;
}
