<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Entity;

use JetBrains\PhpStorm\Pure;
use Doctrine\ORM\Mapping as ORM;
use Talav\CoreBundle\Entity\Traits\HasRelations;
use Doctrine\Common\Collections\{Collection, ArrayCollection};
use Talav\Component\Resource\Model\ResourceTrait;

class RoleInterface implements RoleInterface
{
    use ResourceTrait;
    use HasRelations;

    protected string $name;

    protected Collection $permissions;

    #[Pure] public function __construct(?string $name = null)
    {
        if ($name) {
            $this->name = $name;
        }

        $this->permissions = new ArrayCollection();
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

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(PermissionInterface $permission): self
    {
        if (!$this->permissions->contains($permission))
            $this->permissions->add($permission);
        return $this;
    }

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function hasPermission(string $name): bool
	{
		/** @var Permission $permission */
		foreach ($this->getPermissions() as $permission) {
			if ($name === $permission->getName()) {
				return true;
			}
		}

		return false;
	}
}
