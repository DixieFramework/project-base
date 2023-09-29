<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Security;

interface PermissionLoaderInterface
{
    public function loadByRole(string $roleName): array;
}
