<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Talav\Component\Resource\Model\ResourceInterface;

interface RoleInterface extends ResourceInterface
{
    public function getName(): string;

    public function setName(string $name): self;

    public function getPermissions(): Collection;

    public function addPermission(PermissionInterface $permission): self;

    public function hasPermission(string $name): bool;
}
