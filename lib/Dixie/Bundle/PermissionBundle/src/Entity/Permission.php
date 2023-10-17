<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Entity;

use JetBrains\PhpStorm\Pure;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Talav\Component\Resource\Model\ResourceTrait;

class Permission implements PermissionInterface
{
    use ResourceTrait;

    protected string $name;

    #[ORM\ManyToMany(targetEntity: 'Talav\PermissionBundle\Entity\RoleInterface', inversedBy: 'permissions')]
    #[ORM\JoinTable(name: 'roles_permissions')]
    #[ORM\JoinColumn(name: 'permission_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'role_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    protected Collection $roles;

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
