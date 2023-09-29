<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Security;

interface PermissionStorageInterface
{
    /**
     * Get permissions list
     *
     * @return array<string>
     */
    public function getPermissions(): array;
}
