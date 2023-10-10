<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Entity;

use JetBrains\PhpStorm\Pure;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\PermissionBundle\Repository\RolePermissionRepository;

#[ORM\Entity(repositoryClass: RolePermissionRepository::class)]
#[ORM\Table(name: 'tlv_role_permission')]
class RolePermission
{
//    use ResourceTrait;
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(name: 'permission_id', nullable: false)]
    protected PermissionInterface $permission;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE', nullable: false)]
    protected RoleInterface $role;

    #[Pure] public function __construct()
    {
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

    /**
     * @return RoleInterface
     */
    public function getRole(): RoleInterface
    {
        return $this->role;
    }

    /**
     * @param RoleInterface $role
     */
    public function setRole(RoleInterface $role): void
    {
        $this->role = $role;
    }

    /**
     * @return PermissionInterface
     */
    public function getPermission(): PermissionInterface
    {
        return $this->permission;
    }

    /**
     * @param PermissionInterface $permission
     */
    public function setPermission(PermissionInterface $permission): void
    {
        $this->permission = $permission;
    }


}
