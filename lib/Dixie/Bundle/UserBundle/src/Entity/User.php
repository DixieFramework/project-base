<?php

declare(strict_types=1);

namespace Talav\UserBundle\Entity;

use Doctrine\Common\Collections\
{Collection, ArrayCollection, Criteria};
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Talav\Component\Media\Model\MediaInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\User\Model\AbstractUser;
use Talav\CoreBundle\Entity\Traits\HasRelations;
use Talav\PermissionBundle\Entity\Role;
use Talav\PermissionBundle\Entity\RoleInterface;
use Talav\PermissionBundle\Traits\HasRoles;
use Talav\ProfileBundle\Entity\Friend;
use Talav\ProfileBundle\Model\ProfileInterface;
use Talav\ProfileBundle\Entity\UserFriend;
use Talav\ProfileBundle\Entity\UserMetadata;
use Talav\ProfileBundle\Entity\UserProfileRelation;
use Talav\ProfileBundle\Entity\UserRelation;
use Talav\ProfileBundle\Enum\FriendStatus;
use Talav\UserBundle\Enum\UserFlagKey;
use Talav\UserBundle\Model\UserInterface;

class User extends AbstractUser implements UserInterface
{
    use ResourceTrait;
    use HasRoles, HasRelations;

	/**
	 * @var RoleHierarchyInterface
	 */
	public static $roleHierarchy;

	protected ?ProfileInterface $profile = null;

    #[ORM\OneToOne(targetEntity: MediaInterface::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'media_id')]
    protected ?MediaInterface $avatar = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserFriend::class, cascade: ['persist', 'remove'])]
    private Collection $friendRequests;

    #[ORM\OneToMany(mappedBy: 'friend', targetEntity: UserFriend::class, cascade: ['persist', 'remove'])]
    private Collection $acceptedFriendRequests;

	#[ORM\OneToMany(targetEntity: UserRelation::class, mappedBy: 'owner')]
    protected $sendedUserRelations;

	#[ORM\OneToMany(targetEntity: UserRelation::class, mappedBy: 'receiver')]
    protected $receivedUserRelations;

//    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTime $lastActivityAt = null;

    #[ORM\OneToMany(targetEntity: UserMetadata::class, mappedBy: 'user', orphanRemoval: true, cascade: ['persist'])]
    protected Collection $metadata;

//    #[ORM\ManyToMany(targetEntity: 'Talav\PermissionBundle\Entity\Role')]
    protected Collection $roles;

//    #[ORM\ManyToMany(targetEntity: 'Talav\PermissionBundle\Entity\Permission', indexBy: 'name')]
    protected Collection $permissions;

//    #[ORM\Column(type: 'json', nullable: false)]
    protected array $flags;

    /** ------------ (non mapped) ------------ */
	#[
		Assert\Length(
			min: 8,
			minMessage: 'Le mot de passe doit avoir au moins 8 caractères'
		)
	]
	#[
		Assert\Regex(
			pattern: '/^(?=.*[A-Z])(?=.*\d).+$/',
			message: 'Le mot de passe doit contenir au moins une lettre majuscule et un chiffre'
		)
	]
	protected ?string $plainPassword = null;

    protected bool $sendCreationEmail = false;

    public function __construct()
    {
        parent::__construct();

        $this->friendRequests = new ArrayCollection();
        $this->acceptedFriendRequests = new ArrayCollection();

	    $this->sendedUserRelations = new ArrayCollection();
	    $this->receivedUserRelations = new ArrayCollection();

        $this->metadata = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->permissions = new ArrayCollection();

        $this->flags = [];
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

    public function getAvatar(): ?MediaInterface
    {
        return $this->avatar;
    }

    public function setAvatar(?MediaInterface $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getAvatarName(): ?string
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

    public function getAvatarDescription(): ?string
    {
        return $this->getAvatarName();
    }

    /**
     * @return Collection<int, UserFriend>
     */
    public function getFriends(): Collection
    {
        return new ArrayCollection(array_merge(
            $this->friendRequests->toArray(),
            $this->acceptedFriendRequests->toArray()
        ));
    }

    public function addFriend(UserFriend $friend): self
    {
        if (!$this->friendRequests->contains($friend)) {
            $this->friendRequests[] = $friend;
            $friend->setUser($this);
        }

        return $this;
    }

    public function removeFriend(UserFriend $friend): self
    {
        if ($this->friendRequests->removeElement($friend)) {
            // set the owning side to null (unless already changed)
            if ($friend->getUser() === $this) {
                $friend->setUser(null);
            }
        }

        return $this;
    }

    public function isFriend(self $user): bool
    {
        $criteria = Criteria::create();

        $criteria->orWhere(Criteria::expr()->eq('user', $user));
        $criteria->orWhere(Criteria::expr()->eq('friend', $user));
        $criteria->andWhere(Criteria::expr()->eq('status', FriendStatus::CONFIRMED->name));

        return 1 === $this->getFriends()->matching($criteria)->count();
    }

	/**
	 * @return Collection|UserRelation[]
	 */
	public function getSendedUserRelations(): Collection
	{
		return $this->sendedUserRelations;
	}

	public function addSendedUserRelation(UserRelation $sendedUserRelation): self
	{
		if (!$this->sendedUserRelations->contains($sendedUserRelation)) {
			$this->sendedUserRelations[] = $sendedUserRelation;
			$sendedUserRelation->setOwner($this);
		}

		return $this;
	}

	public function removeSendedUserRelation(UserRelation $sendedUserRelation): self
	{
		if ($this->sendedUserRelations->removeElement($sendedUserRelation)) {
			// set the owning side to null (unless already changed)
			if ($sendedUserRelation->getOwner() === $this) {
				$sendedUserRelation->setOwner(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection|UserRelation[]
	 */
	public function getReceivedUserRelations(): Collection
	{
		return $this->receivedUserRelations;
	}

	public function addReceivedUserRelation(UserRelation $receivedUserRelation): self
	{
		if (!$this->receivedUserRelations->contains($receivedUserRelation)) {
			$this->receivedUserRelations[] = $receivedUserRelation;
			$receivedUserRelation->setReceiver($this);
		}

		return $this;
	}

	public function removeReceivedUserRelation(UserRelation $receivedUserRelation): self
	{
		if ($this->receivedUserRelations->removeElement($receivedUserRelation)) {
			// set the owning side to null (unless already changed)
			if ($receivedUserRelation->getReceiver() === $this) {
				$receivedUserRelation->setReceiver(null);
			}
		}

		return $this;
	}

    public function setLastActivityAt(?\DateTime $lastActivityAt): self
    {
        $this->lastActivityAt = $lastActivityAt;

        return $this;
    }

    public function getLastActivityAt(): ?\DateTime
    {
        return $this->lastActivityAt;
    }

    public function getMetadata(string $key): ?UserMetadata
    {
        foreach ($this->metadata as $metadata) {
            if ($metadata->getName() == $key) {
                return $metadata;
            }
        }

        return null;
    }

    public function getMetadataValue(string $key): string
    {
        return $this->getMetadata($key)?->getValue() ?: '';
    }

    public function setMetadata(string $key, string $value): self
    {
        $metadata = $this->getMetadata($key);

        if (!$metadata) {
            $metadata = new UserMetadata($this, $key, $value);
            $this->metadata->add($metadata);
        }

        $metadata->setValue($value);

        return $this;
    }

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function getRolesRelation(): Collection
    {
        return $this->roles;
    }

    public function getFirstRole(): ?RoleInterface
    {
        return $this->roles->first() ?: null;
    }

	public function getRoles(): array
	{
		$roles = [];
		$rolesDB = $this->roles->toArray();
		foreach ($rolesDB as $role) {
			$roles[] = $role->getName();
		}

		return $roles;
	}
    //public function getRoles(): array
    //{
    //    return ['ROLE_USER']; // Default role for any user.
    //}

	/**
	 * {@inheritdoc}
	 */
	public function hasRole($role): bool
	{
		if ($role instanceof RoleInterface) {
			$role = $role->getName();
		}

		if (is_string($role)) {
			return in_array($role, array_values(self::$roleHierarchy->getReachableRoleNames($this->getRoles())));
		}

		if (is_array($role)) {
			foreach ($role as $item)
				if ($this->hasRole($item)) return true;
			return false;
		}
	}

	public function removeRole(string $role): void
	{
		if ($item = $this->findUserRole($role)) {
			$this->roles->removeElement($item);
		}
	}

	public function findUserRole(string $role): ?RoleInterface
	{
		/** @var RoleInterface $item */
		foreach ($this->roles as $item) {
			if ($role == $item->getName()) {
				return $item;
			}
		}

		return null;
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
     * Setzt eine Userflag. Wenn nicht vorhanden, wird sie neu generiert.
     *
     * @param string $flagKey
     * @param mixed  $flagValue
     */
    protected function setFlagValue(UserFlagKey $flagKey, $flagValue)
    {
        $this->flags[$flagKey->value] = $flagValue;
    }

    /**
     * get UserFlag by Key.
     *
     * @param string $flagKey
     */
    protected function getFlagValue(UserFlagKey $flagKey): bool
    {
        if (is_array($this->flags) && !empty($this->flags) && array_key_exists($flagKey->value, $this->flags)) {
            return (bool) $this->flags[$flagKey->value];
        }

        return false;
    }

    public function isProfileCompleted(): bool
    {
        return $this->getFlagValue(UserFlagKey::PROFILE_COMPLETED);
    }

    /**
     * @param bool $profileCompleted
     */
    public function setProfileCompleted($profileCompleted): UserInterface
    {
        $this->setFlagValue(UserFlagKey::PROFILE_COMPLETED, $profileCompleted);

        return $this;
    }

	public function getPlainPassword(): ?string
	{
		return $this->plainPassword;
	}

	public function setPlainPassword(?string $plainPassword): UserInterface
	{
		$this->plainPassword = $plainPassword;

		return $this;
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
