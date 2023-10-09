<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Entity;

use JetBrains\PhpStorm\Pure;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Talav\Component\Resource\Model\ResourceTrait;

class RolePermission
{
    use ResourceTrait;

    protected $permission;

    protected $role;

    #[Pure] public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }
}
