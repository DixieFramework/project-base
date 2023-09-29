<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PermissionStorage implements PermissionStorageInterface
{
    private TokenStorageInterface $tokenStorage;

    private PermissionLoaderInterface $permissionLoader;

    public function __construct(TokenStorageInterface $tokenStorage, PermissionLoaderInterface $permissionLoader)
    {
        $this->tokenStorage     = $tokenStorage;
        $this->permissionLoader = $permissionLoader;
    }

    public function getPermissions(): array
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            return [];
        }

        $permissions = array_map(function (string $roleName) {
            return $this->permissionLoader->loadByRole($roleName);
        }, $token->getRoleNames());

        return array_values(array_unique(array_merge(...$permissions)));
    }
}
