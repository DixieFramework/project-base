<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Talav\PermissionBundle\Entity\RoleInterface;
use Talav\PermissionBundle\Entity\RolePermission;

/**
 * @extends \Doctrine\ORM\EntityRepository<RolePermission>
 */
class RolePermissionRepository extends EntityRepository
{
    public function saveRolePermission(RolePermission $permission): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($permission);
        $entityManager->flush();
    }

    public function findRolePermission(RoleInterface $role, string $permission): ?RolePermission
    {
        return $this->findOneBy(['role' => $role, 'permission' => $permission]);
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
