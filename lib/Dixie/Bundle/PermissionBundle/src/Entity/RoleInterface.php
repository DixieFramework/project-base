<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Talav\Component\Resource\Model\ResourceInterface;

interface RoleInterface extends ResourceInterface
{
	/**
	 * The administrator role name.
	 */
	final public const ROLE_ADMIN = 'ROLE_ADMIN';

	/**
	 * The super administrator role name.
	 */
	final public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

	/**
	 * The user role name.
	 */
	final public const ROLE_USER = 'ROLE_USER';

    public function getName(): string;

    public function setName(string $name): self;

    public function getPermissions(): Collection;

    public function addPermission(PermissionInterface $permission): self;

    public function hasPermission(string $name): bool;
}
