<?php

declare(strict_types=1);

namespace Talav\UserBundle\Entity;

use Doctrine\Common\Collections\{Collection, ArrayCollection};
use Doctrine\ORM\Mapping as ORM;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\User\Model\AbstractUser;
use Talav\CoreBundle\Entity\Traits\HasRelations;
use Talav\PermissionBundle\Entity\Role;
use Talav\PermissionBundle\Traits\HasRoles;
use Talav\ProfileBundle\Entity\ProfileInterface;
use Talav\UserBundle\Model\UserInterface;

class User extends AbstractUser implements UserInterface
{
    use ResourceTrait;

    use HasRoles, HasRelations;

    protected ?ProfileInterface $profile = null;

//    #[ORM\ManyToMany(targetEntity: 'Talav\PermissionBundle\Entity\Role')]
    protected Collection $roles;

//    #[ORM\ManyToMany(targetEntity: 'Talav\PermissionBundle\Entity\Permission', indexBy: 'name')]
    protected Collection $permissions;

    /** ------------ (non mapped) ------------ */
    protected bool $sendCreationEmail = false;

    public function __construct()
    {
        parent::__construct();

        $this->roles = new ArrayCollection();
        $this->permissions = new ArrayCollection();
    }

    /**
     * @return ProfileInterface|null
     */
    public function getProfile(): ?ProfileInterface
    {
        return $this->profile;
    }

    /**
     * @param ProfileInterface|null $profile
     */
    public function setProfile(?ProfileInterface $profile): void
    {
        // prevent setting a new profile once set
        if (! $this->getProfile()) {
            $this->profile = $profile;
            $profile->setUser($this);
        }
    }

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function getRolesRelation(): Collection
    {
        return $this->roles;
    }

    public function getFirstRole(): ?Role
    {
        return $this->roles->first() ?: null;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER']; // Default role for any user.
    }

    public function setSendCreationEmail(bool $send): UserInterface
    {
        $this->sendCreationEmail = $send;

        return $this;
    }

    public function getSendCreationEmail(): bool
    {
        return $this->sendCreationEmail;
    }
}
