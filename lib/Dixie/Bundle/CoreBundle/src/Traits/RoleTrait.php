<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

use Talav\CoreBundle\Interfaces\RoleInterface;
use Talav\CoreBundle\Utils\StringUtils;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trait for class implementing the {@link RoleInterface} interface.
 *
 * @psalm-require-implements RoleInterface
 */
trait RoleTrait
{
    use RightsTrait;

    /**
     * The role name.
     */
    #[Assert\Length(max: 25)]
    #[Assert\Choice([RoleInterface::ROLE_USER, RoleInterface::ROLE_ADMIN, RoleInterface::ROLE_SUPER_ADMIN])]
    #[ORM\Column(length: 25, nullable: true)]
    private ?string $role = null;

    /**
     * Gets the role.
     *
     * @see RoleInterface
     *
     * @pslam-return RoleInterface::ROLE_*
     */
    public function getRole(): string
    {
        return $this->role ?? RoleInterface::ROLE_USER;
    }

    /**
     * @return string[]
     *
     * @see UserInterface
     *
     * @pslam-return RoleInterface::ROLE_*[]
     */
    public function getRoles(): array
    {
        return [$this->getRole()];
    }

    /**
     * @see RoleInterface
     *
     * @psalm-param RoleInterface::ROLE_* $role
     */
    public function hasRole(string $role): bool
    {
        return StringUtils::equalIgnoreCase($role, $this->getRole());
    }

    /**
     * @see RoleInterface
     */
    public function isAdmin(): bool
    {
        return $this->isSuperAdmin() || $this->hasRole(RoleInterface::ROLE_ADMIN);
    }

    /**
     * @see RoleInterface
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(RoleInterface::ROLE_SUPER_ADMIN);
    }

    /**
     * Sets the role.
     *
     * @psalm-param RoleInterface::ROLE_*|null $role
     */
    public function setRole(?string $role): static
    {
        // null or default?
        if (null === $role || StringUtils::equalIgnoreCase(RoleInterface::ROLE_USER, $role)) {
            $this->role = null;
        } else {
            $this->role = \strtoupper($role);
        }

        return $this;
    }
}
