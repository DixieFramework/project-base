<?php

declare(strict_types=1);

namespace Talav\UserBundle\Entity;

use Doctrine\Common\Collections\
{Collection, ArrayCollection, Criteria};
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Talav\AvatarBundle\Model\UserAvatarInterface;
use Talav\AvatarBundle\Model\UserCoverInterface;
use Talav\Component\Media\Model\MediaInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\User\Model\AbstractUser;
use Talav\Component\User\Model\RolesInterface;
use Talav\Component\User\Model\RolesTrait;
use Talav\CoreBundle\Entity\Traits\HasRelations;
use Talav\PermissionBundle\Entity\Role;
use Talav\PermissionBundle\Entity\RoleInterface;
use Talav\PermissionBundle\Traits\HasRoles;
use Talav\PostBundle\Entity\Bookmark;
use Talav\PostBundle\Entity\PostInterface;
use Talav\ProfileBundle\Entity\LikeInterface;
use Talav\ProfileBundle\Entity\ProfileInterface;
use Talav\ProfileBundle\Entity\UserFriend;
use Talav\ProfileBundle\Entity\UserMetadata;
use Talav\ProfileBundle\Entity\UserProfileRelation;
use Talav\ProfileBundle\Entity\UserRelation;
use Talav\ProfileBundle\Enum\FriendStatus;
use Talav\UserBundle\Doctrine\EntityListener\UserEmailEntityListener;
use Talav\UserBundle\Doctrine\EntityListener\UserEntityListener;
use Talav\UserBundle\Enum\UserFlagKey;
use Talav\UserBundle\Model\UserInterface;
use Talav\UserBundle\Model\UserRolesInterface;
use Talav\UserBundle\Model\UserRolesTrait;

//#[ORM\InheritanceType('SINGLE_TABLE')]
//#[ORM\DiscriminatorColumn(name: 'class', type: 'string')]
//#[ORM\DiscriminatorMap(['user' => 'User', 'ad' => 'ActiveDirectoryUser'])]
#[ORM\UniqueConstraint(name: 'unique_user_email', columns: ['email'])]
//#[ORM\UniqueConstraint(name: 'unique_user_username', columns: ['username'])]
#[UniqueEntity(fields: ['email'], message: 'email.already_used')]
//#[UniqueEntity(fields: ['username'], message: 'username.already_used')]
//#[ORM\EntityListeners([UserEmailEntityListener::class, UserEntityListener::class])]
#[ORM\MappedSuperclass]
abstract class User extends AbstractUser implements UserInterface, UserAvatarInterface, UserCoverInterface, UserRolesInterface, EquatableInterface, \Serializable
{
    use ResourceTrait;
    use UserRolesTrait;

    use HasRoles, HasRelations;

	//use RolesTrait;

	/**
	 * @var RoleHierarchyInterface
	 */
	public static $roleHierarchy;

	protected ?ProfileInterface $profile = null;

    #[ORM\OneToOne(targetEntity: MediaInterface::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'avatar_media_id')]
    protected ?MediaInterface $avatar = null;

    #[ORM\OneToOne(targetEntity: MediaInterface::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'cover_media_id')]
    protected ?MediaInterface $cover = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserFriend::class, cascade: ['persist', 'remove'])]
    protected Collection $friendRequests;

    #[ORM\OneToMany(mappedBy: 'friend', targetEntity: UserFriend::class, cascade: ['persist', 'remove'])]
    protected Collection $acceptedFriendRequests;

	#[ORM\OneToMany(targetEntity: UserRelation::class, mappedBy: 'owner')]
    protected $sendedUserRelations;

	#[ORM\OneToMany(targetEntity: UserRelation::class, mappedBy: 'receiver')]
    protected $receivedUserRelations;

//    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTimeImmutable $lastActivityAt = null;

    #[ORM\OneToMany(targetEntity: UserMetadata::class, mappedBy: 'user', orphanRemoval: true, cascade: ['persist'])]
    protected Collection $metadata;

//    #[ORM\ManyToMany(targetEntity: 'Talav\PermissionBundle\Entity\Role')]
    protected Collection $userRoles;

//    #[ORM\ManyToMany(targetEntity: 'Talav\PermissionBundle\Entity\Permission', indexBy: 'name')]
    protected Collection $permissions;

	#[ORM\OneToMany(mappedBy: 'author', targetEntity: PostInterface::class, orphanRemoval: true)]
	protected $posts;

	#[ORM\OneToMany(mappedBy: 'user', targetEntity: Bookmark::class, orphanRemoval: true)]
	protected $bookmarks;

	#[ORM\OneToMany(mappedBy: 'user', targetEntity: LikeInterface::class, orphanRemoval: true)]
	protected $likes;

//    #[ORM\Column(type: 'json', nullable: false)]
    protected array $flags = [];

    #[ORM\Column(nullable: true)]
    private null|string $state = null;

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
        $this->friendRequests = new ArrayCollection();
        $this->acceptedFriendRequests = new ArrayCollection();

	    $this->sendedUserRelations = new ArrayCollection();
	    $this->receivedUserRelations = new ArrayCollection();

        $this->metadata       = new ArrayCollection();
        $this->userRoles      = new ArrayCollection();
        $this->permissions    = new ArrayCollection();

	    $this->posts          = new ArrayCollection();
	    $this->bookmarks      = new ArrayCollection();
	    $this->likes          = new ArrayCollection();

        $this->flags = [];

        parent::__construct();

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

    public function getCover(): ?MediaInterface
    {
        return $this->cover;
    }

    public function setCover(?MediaInterface $avatar): void
    {
        $this->cover = $avatar;
    }

    public function getCoverName(): ?string
    {
        return $this->getFirstName().' '.$this->getLastName().'coverImage';
    }

    public function getCoverDescription(): ?string
    {
        return $this->getCoverName();
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

    public function setLastActivityAt(?\DateTimeImmutable $lastActivityAt): self
    {
        $this->lastActivityAt = $lastActivityAt;

        return $this;
    }

    public function getLastActivityAt(): ?\DateTimeImmutable
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
        return $this->userRoles;
    }

    public function getFirstRole(): ?RoleInterface
    {
        return $this->roles->first() ?: null;
    }

//	public function getRoles(): array
//	{
//		$roles = [];
//		$rolesDB = $this->roles->toArray();
//		foreach ($rolesDB as $role) {
//			$roles[] = $role->getName();
//		}
//
//		return $roles;
//	}
    //public function getRoles(): array
    //{
    //    return ['ROLE_USER']; // Default role for any user.
    //}

//	/**
//	 * {@inheritdoc}
//	 */
//	public function hasRole($role): bool
//	{
//		if ($role instanceof RoleInterface) {
//			$role = $role->getName();
//		}
//
//		if (is_string($role)) {
//            return in_array($role, array_values($this->getRoles()));
////            return in_array($role, array_values(self::$roleHierarchy->getReachableRoleNames($this->getRoles())));
//		}
//
//		if (is_array($role)) {
//			foreach ($role as $item)
//				if ($this->hasRole($item)) return true;
//			return false;
//		}
//	}

//	public function removeRole(string $role): void
//	{
//		if ($item = $this->findUserRole($role)) {
//			$this->roles->removeElement($item);
//		}
//	}

//	public function findUserRole(string $role): ?RoleInterface
//	{
//		/** @var RoleInterface $item */
//		foreach ($this->roles as $item) {
//			if ($role == $item->getName()) {
//				return $item;
//			}
//		}
//
//		return null;
//	}

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

	public function getPosts(): Collection
	{
		return $this->posts;
	}

	public function addPost(PostInterface $post): self
	{
		if (!$this->posts->contains($post)) {
			$this->posts[] = $post;
			$post->setAuthor($this);
		}

		return $this;
	}

	public function removePost(PostInterface $post): self
	{
		if ($this->posts->contains($post)) {
			$this->posts->removeElement($post);
			if ($post->getAuthor() === $this) {
				$post->setAuthor(null);
			}
		}

		return $this;
	}

	public function getBookmarks(): Collection
	{
		return $this->bookmarks;
	}

	public function addBookmark(Bookmark $bookmark): self
	{
		if (!$this->bookmarks->contains($bookmark)) {
			$this->bookmarks[] = $bookmark;
			$bookmark->setUser($this);
		}

		return $this;
	}

	public function removeBookmark(Bookmark $bookmark): self
	{
		if ($this->bookmarks->contains($bookmark)) {
			$this->bookmarks->removeElement($bookmark);
			if ($bookmark->getUser() === $this) {
				$bookmark->setUser(null);
			}
		}

		return $this;
	}

	public function getLikes(): Collection
	{
		return $this->likes;
	}

	public function addLike(LikeInterface $like): self
	{
		if (!$this->likes->contains($like)) {
			$this->likes[] = $like;
			$like->setUser($this);
		}

		return $this;
	}

	public function removeLike(LikeInterface $like): self
	{
		if ($this->likes->contains($like)) {
			$this->likes->removeElement($like);
			if ($like->getUser() === $this) {
				$like->setUser(null);
			}
		}

		return $this;
	}


	/**
     * Add arbitrary UserFlag.
     *
     * @param string $key
     * @param bool   $value
     */
    public function addFlag($key, $value)
    {
        $this->setFlagValue($key, $value);
    }

    /**
     * Return arbitrary UserFlag.
     *
     * @param string $key
     */
    public function getFlag($key): bool
    {
        return $this->getFlagValue($key);
    }

    /**
     * Sets a user flag. If not present, it will be regenerated.
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

	public function isNewUser(): bool
	{
		return $this->getFlagValue(UserFlagKey::IS_NEW_USER);
	}

	/**
	 * @param bool $isNewUser
	 */
	public function setIsNewUser($isNewUser): UserInterface
	{
		$this->setFlagValue(UserFlagKey::IS_NEW_USER, $isNewUser);

		return $this;
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

    /**
     * @return int
     */
    public function getProfileCompletion()
    {
        $infos = [
            $this->firstName, $this->lastName, $this->profile->getGender(), $this->profile->getBirthDate()
        ];

        $completion = 0;
        $count = 0;

        foreach ($infos as $value) {
            ++$count;

            if (!empty($value)) {
                ++$completion;
            }
        }

        return round($completion / $count, 2) * 100;
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

    public function getState(): null|string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function isEqualTo(SymfonyUserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        return (string)$this->id === (string)$user->getId()
            && $this->password === $user->getPassword()
            && $this->salt === $user->getSalt()
            && (string)$this->username === (string)$user->getUsername()
            && $this->enabled === $user->isEnabled();
    }
//    public function isEqualTo(\Symfony\Component\Security\Core\User\UserInterface $user): bool
//    {
//        if (!$user instanceof self) {
//            return false;
//        }
//
//        if ($this->id !== $user->getId()) {
//            return false;
//        }
//
//        if ($this->password !== $user->getPassword()) {
//            return false;
//        }
//
//        if ($this->salt !== $user->getSalt()) {
//            return false;
//        }
//
//        if ($this->username !== $user->getUsername()) {
//            return false;
//        }
//
//        if ($this->isEnabled() !== $user->isEnabled()) {
//            return false;
//        }
//
//        if ($this->isVerified() !== $user->isVerified()) {
//            return false;
//        }
//
//        return true;
//    }

//    public function __serialize(): array
//    {
//        return [
//            $this->id,
//            $this->username,
//            $this->usernameCanonical,
//            $this->email,
//            $this->emailCanonical,
//            $this->password,
//            $this->salt,
//            $this->enabled,
//            $this->state
//        ];
//    }
//
//    public function __unserialize(array $data): void
//    {
//        if (8 === count($data)) {
//            [
//                $this->id,
//                $this->username,
//                $this->usernameCanonical,
//                $this->email,
//                $this->emailCanonical,
//                $this->password,
//                $this->salt,
//                $this->enabled,
//                $this->state
//            ] = $data;
//        }
//    }

    /**
     * Serializes the user just with the id, as it is enough.
     *
     * @see http://php.net/manual/en/serializable.serialize.php
     *
     * @return string The string representation of the object or null
     */
    public function serialize(): string
    {
        return \serialize(
            [
                $this->id,
                $this->password,
                $this->salt,
                $this->username,
                $this->usernameCanonical,
                $this->email,
                $this->emailCanonical,
                $this->enabled,
                $this->state
            ]
        );
    }

    /**
     * Constructs the object.
     *
     * @see http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized The string representation of the object
     */
    public function unserialize($serialized): void
    {
        list(
            $this->id, $this->password, $this->salt, $this->username, $this->usernameCanonical, $this->email, $this->emailCanonical, $this->enabled, $this->state
            ) = \unserialize($serialized);
    }
}
