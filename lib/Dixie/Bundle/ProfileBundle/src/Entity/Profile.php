<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\Resource\Model\TimestampableTrait;
use Talav\Component\User\Model\UserInterface;
use Talav\ProfileBundle\Enum\Gender;
use Talav\ProfileBundle\Model\ProfileInterface;

class Profile implements ProfileInterface
{
    use ResourceTrait;
    use TimestampableTrait;

    protected ?string $firstName = null;

    protected ?string $lastName = null;

//    #[ORM\Column(type: 'string', enumType: Gender::class)]
    protected Gender $gender;

    protected ?\DateTimeInterface $birthdate = null;

    protected ?string $bio = null;

    protected UserInterface $user;

	protected Collection $requester;

	protected Collection $requestee;

	protected Collection $friendships;

    #[ORM\OneToMany(mappedBy: 'blocker', targetEntity: ProfileBlock::class, cascade: [
        'persist',
        'remove',
    ], orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    public Collection $blocks;

    #[ORM\OneToMany(mappedBy: 'blocked', targetEntity: ProfileBlock::class, cascade: [
        'persist',
        'remove',
    ], orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    public ?Collection $blockers;

    #[ORM\OneToMany(mappedBy: 'profile', targetEntity: Suspension::class, orphanRemoval: true)]
    protected ?Collection $suspensions = null;

    /**
     * @var ArrayCollection
     */
    private readonly Collection $relationships;

    public function __construct()
    {
	    $this->requester = new ArrayCollection();
	    $this->requestee = new ArrayCollection();
	    $this->friendships = new ArrayCollection();

        $this->blocks = new ArrayCollection();
        $this->blockers = new ArrayCollection();

        $this->suspensions = new ArrayCollection();
        $this->relationships = new ArrayCollection();
    }


    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getGender(): Gender
    {
        return $this->gender;
    }

    public function setGender(Gender $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return ?\DateTimeInterface
     */
    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    /**
     * @param ?\DateTimeInterface $birthdate
     */
    public function setBirthdate(?\DateTimeInterface $birthdate): void
    {
        $this->birthdate = $birthdate;
    }

    /**
     * @return string|null
     */
    public function getBio(): ?string
    {
        return $this->bio;
    }

    /**
     * @param string|null $bio
     */
    public function setBio(?string $bio): void
    {
        $this->bio = $bio;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     *
     * @return ProfileInterface
     */
    public function setUser(UserInterface $user): ProfileInterface
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setProfile(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getProfile() !== $this) {
            $user->setProfile($this);
        }

        $this->user = $user;

        return $this;
    }

	/**
	 * @return Collection<int, FriendshipRequest>
	 */
	public function getRequestsMadeByProfile(): Collection
	{
		return $this->requester;
	}

	public function addRequester(FriendshipRequest $requester): self
	{
		if (!$this->requester->contains($requester)) {
			$this->requester->add($requester);
			$requester->setRequester($this);
		}

		return $this;
	}

	public function removeRequester(FriendshipRequest $requester): self
	{
		if ($this->requester->removeElement($requester)) {
			if ($requester->getRequester() === $this) {
				$requester->setRequester(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, FriendshipRequest>
	 */
	public function getRequestsMadeToProfile(): Collection
	{
		return $this->requestee;
	}

	public function addRequestee(FriendshipRequest $requestee): self
	{
		if (!$this->requestee->contains($requestee)) {
			$this->requestee->add($requestee);
			$requestee->setRequestee($this);
		}

		return $this;
	}

	public function removeRequestee(FriendshipRequest $requestee): self
	{
		if ($this->requestee->removeElement($requestee)) {
			// set the owning side to null (unless already changed)
			if ($requestee->getRequestee() === $this) {
				$requestee->setRequestee(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, Friendship>
	 */
	public function getFriendships(): Collection
	{
		return $this->friendships;
	}

	public function addFriendship(Friendship $friendship): self
	{
		if (!$this->friendships->contains($friendship)) {
			$this->friendships->add($friendship);
			$friendship->setProfile($this);
		}

		return $this;
	}

	public function removeFriendship(Friendship $friendship): self
	{
		if ($this->friendships->removeElement($friendship)) {
			if ($friendship->getProfile() === $this) {
				$friendship->setProfile(null);
			}
		}

		return $this;
	}

	/**
	 * Check if profile with id = $friendId is a friend of current user
	 *
	 * @param int $friendId
	 * @return bool
	 */
	public function isFriend(int $friendId): bool
	{
		$friendships = $this->getFriendships()->filter(
			function ($friendship) use ($friendId) {
				return $friendship->getFriend()->getId() == $friendId;
			}
		);

		return (bool)$friendships->count();
	}

	/**
	 * Check if current user has incoming friendship request from profile with id = $profileId
	 *
	 * @param int $profileId
	 * @return bool
	 */
	public function hasIncomingRequest(int $profileId): bool
	{
		return (bool)$this->getRequestsMadeToProfile()
			->filter(
				function ($request) use ($profileId) {
					/** @var FriendshipRequest $request */
					return $request->getRequester()->getId() == $profileId;
				}
			)->count();
	}

	/**
	 * Check if current user has outgoing friendship request to profile with id = $profileId
	 *
	 * @param int $profileId
	 * @return bool
	 */
	public function hasOutgoingRequest(int $profileId): bool
	{
		return (bool)$this->getRequestsMadeByProfile()
			->filter(
				function ($request) use ($profileId) {
					/** @var FriendshipRequest $request */
					return $request->getRequestee()->getId() == $profileId;
				}
			)->count();
	}


    public function isBlocker(ProfileInterface $user): bool
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('blocker', $user));

        return $user->blockers->matching($criteria)->count() > 0;
    }

    public function block(ProfileInterface $blocked): self
    {
        if (!$this->isBlocked($blocked)) {
            $this->blocks->add($userBlock = new ProfileBlock($this, $blocked));

            if (!$blocked->blockers->contains($userBlock)) {
                $blocked->blockers->add($userBlock);
            }
        }

        return $this;
    }

    /**
     * Returns whether or not the given user is blocked by the user this method is called on.
     */
    public function isBlocked(ProfileInterface $user): bool
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('blocked', $user));

        return $this->blocks->matching($criteria)->count() > 0;
    }
    public function unblock(ProfileInterface $blocked): void
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('blocked', $blocked));

        /**
         * @var $userBlock ProfileBlock
         */
        $userBlock = $this->blocks->matching($criteria)->first();

        if ($this->blocks->removeElement($userBlock)) {
            if ($userBlock->blocker === $this) {
                $blocked->blockers->removeElement($this);
            }
        }
    }

    public function isSuspended(): bool
    {
        /** @var Collection<int, Suspension>|null $suspensions */
        $suspensions = $this->getSuspensions();

        if (null === $suspensions) {
            return false;
        }

        $suspension = $suspensions->last();

        if (!$suspension) {
            return false;
        }

        return $suspension->isActive();
    }

    /**
     * @return Collection<int, Suspension>|null
     */
    public function getSuspensions(): ?Collection
    {
        return $this->suspensions;
    }

    public function addSuspension(Suspension $suspension): self
    {
//        Psl\invariant(!$this->isSuspended(), 'Unable to suspend an already suspended user.');

        if (null !== $this->suspensions && !$this->suspensions->contains($suspension)) {
            $this->suspensions[] = $suspension;
            $suspension->setProfile($this);
        }

        return $this;
    }

    public function removeSuspension(Suspension $suspension): self
    {
        if (null !== $this->suspensions && $this->suspensions->contains($suspension)) {
            $this->suspensions->removeElement($suspension);
            // set the owning side to null (unless already changed)
            if ($suspension->getProfile() === $this) {
                $suspension->setProfile(null);
            }
        }

        return $this;
    }

    public function getRelationships(): Collection
    {
        return $this->relationships;
    }

    /**
     * Checks whether the user is suspended.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool true if the user is not suspended, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked(): bool
    {
        return !($this->isSuspended() || !$this->getUser()->isEnabled() || !$this->getUser()->isBanned());
    }
}
