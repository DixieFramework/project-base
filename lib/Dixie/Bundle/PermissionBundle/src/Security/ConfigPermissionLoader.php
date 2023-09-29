<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Security;

class ConfigPermissionLoader implements PermissionLoaderInterface
{
    /**
     * @var \string[][]
     */
    private array $config;

    /**
     * @param array<string, array<string>> $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return array|string[]
     */
    public function loadByRole(string $roleName): array
    {
        return $this->config[$roleName] ?? [];
    }
}
