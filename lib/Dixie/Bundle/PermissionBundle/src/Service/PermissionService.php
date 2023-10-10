<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Talav\PermissionBundle\Entity\RoleInterface;
use Talav\PermissionBundle\Entity\RolePermission;
use Talav\PermissionBundle\Repository\RolePermissionRepository;

/**
 * Caches permissions, which rarely change once Kimai is setup.
 *
 * @final
 */
class PermissionService
{
    /**
     * @var null|array<int, array<string, string|bool>>
     */
    private ?array $cacheAll = null;

    public function __construct(
        private RolePermissionRepository $repository,
        private CacheInterface $cache
    ) {
    }

    public function saveRolePermission(RolePermission $permission): void
    {
        $this->repository->saveRolePermission($permission);
        $this->cache->delete('permissions');
    }

    public function findRolePermission(RoleInterface $role, string $permission): ?RolePermission
    {
        return $this->repository->findRolePermission($role, $permission);
    }

    /**
     * @return array<int, array<string, string|bool>>
     */
    public function getPermissions(): array
    {
        return $this->repository->getAllAsArray();

        $this->cacheAll = null;
        if ($this->cacheAll === null) {
            $this->cacheAll = $this->cache->get('permissions', function (ItemInterface $item) {
                $item->expiresAfter(86400); // one day

                return $this->repository->getAllAsArray();
            });
        }

        return $this->cacheAll;
    }
}
