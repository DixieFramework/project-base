<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use OpenApi\Attributes as OA;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\Resource\Model\TimestampableTrait;
use Talav\Component\User\Model\UserInterface;
use Talav\ProfileBundle\Enum\Gender;

#[ORM\MappedSuperclass]
abstract class Profile implements ProfileInterface
{
    use ResourceTrait;
    use TimestampableTrait;

    protected ?string $firstName = null;

    protected ?string $lastName = null;

//    #[ORM\Column(type: 'string', enumType: Gender::class)]
    protected Gender $gender = Gender::X;

    protected ?\DateTimeInterface $birthdate = null;

    protected ?string $bio = null;

    protected UserInterface $user;

	/**
	 * User preferences
	 *
	 * List of preferences for this user, required ones have dedicated fields/methods
	 *
	 * This Collection can be null for one edge case ONLY:
	 * if a currently logged-in user will be deleted and then refreshed from the session from one of the UserProvider
	 * e.g. see LdapUserProvider::refreshUser() it might crash if $user->getPreferenceValue() is called
	 *
	 * @var Collection<UserPreference>|null
	 */
	#[ORM\OneToMany(mappedBy: 'profile', targetEntity: UserPreference::class, cascade: ['persist'])]
	protected ?Collection $preferences = null;

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

	#[ORM\OneToMany(mappedBy: 'sender', targetEntity: ReportInterface::class, orphanRemoval: true)]
	protected ?Collection $reports;

	#[ORM\OneToMany(mappedBy: 'accused', targetEntity: ReportInterface::class, orphanRemoval: true)]
	protected ?Collection $accusations;

    #[ORM\OneToMany(mappedBy: 'profile', targetEntity: Suspension::class, orphanRemoval: true)]
    protected ?Collection $suspensions = null;

    /**
     * @var ArrayCollection
     */
    private readonly Collection $relationships;

    public function __construct()
    {
	    $this->preferences = new ArrayCollection();
	    $this->requester = new ArrayCollection();
	    $this->requestee = new ArrayCollection();
	    $this->friendships = new ArrayCollection();

        $this->blocks = new ArrayCollection();
        $this->blockers = new ArrayCollection();

	    $this->reports = new ArrayCollection();
	    $this->accusations = new ArrayCollection();
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

	public function setTimezone(?string $timezone): void
	{
		if ($timezone === null) {
			$timezone = date_default_timezone_get();
		}
		$this->setPreferenceValue(UserPreference::TIMEZONE, $timezone);
	}

	/**
	 * @param string $name
	 * @param bool|int|float|string|null $default
	 * @param bool $allowNull
	 * @return bool|int|float|string|null
	 */
	public function getPreferenceValue(string $name, mixed $default = null, bool $allowNull = true): bool|int|float|string|null
	{
		$preference = $this->getPreference($name);
		if (null === $preference) {
			return $default;
		}

		$value = $preference->getValue();

		return $allowNull ? $value : ($value ?? $default);
	}

	/**
	 * @param UserPreference $preference
	 * @return ProfileInterface
	 */
	public function addPreference(UserPreference $preference): ProfileInterface
	{
		if (null === $this->preferences) {
			$this->preferences = new ArrayCollection();
		}

		$this->preferences->add($preference);
		$preference->setProfile($this);

		return $this;
	}

	/**
	 * Read-only list of of all visible user preferences.
	 *
	 * @internal only for API usage
	 * @return UserPreference[]
	 */
	#[Serializer\VirtualProperty]
	#[Serializer\SerializedName('preferences')]
	#[Serializer\Groups(['User_Entity'])]
//	#[OA\Property(type: 'array', items: new OA\Items(ref: '#/components/schemas/UserPreference'))]
	public function getVisiblePreferences(): array
	{
		// hide all internal preferences, which are either available in other fields
		// or which are only used within the Kimai UI
		$skip = [
			UserPreference::TIMEZONE,
			UserPreference::LOCALE,
			UserPreference::SKIN,
			'calendar_initial_view',
			'login_initial_view',
			'update_browser_title',
			'daily_stats',
			'export_decimal',
		];

		$all = [];
		foreach ($this->preferences as $preference) {
			if ($preference->isEnabled() && !\in_array($preference->getName(), $skip)) {
				$all[] = $preference;
			}
		}

		return $all;
	}

	/**
	 * @return Collection<UserPreference>
	 */
	public function getPreferences(): Collection
	{
		return $this->preferences;
	}

	/**
	 * @param iterable<UserPreference> $preferences
	 * @return ProfileInterface
	 */
	public function setPreferences(iterable $preferences): ProfileInterface
	{
		$this->preferences = new ArrayCollection();

		foreach ($preferences as $preference) {
			$this->addPreference($preference);
		}

		return $this;
	}

	/**
	 * @param string $name
	 * @param bool|int|string|float|null $value
	 */
	public function setPreferenceValue(string $name, $value = null): void
	{
		$pref = $this->getPreference($name);

		if (null === $pref) {
			$pref = new UserPreference($name);
			$this->addPreference($pref);
		}

		$pref->setValue($value);
	}

	public function getPreference(string $name): ?UserPreference
	{
		if ($this->preferences === null) {
			return null;
		}

		foreach ($this->preferences as $preference) {
			if ($preference->matches($name)) {
				return $preference;
			}
		}

		return null;
	}

	#[Serializer\VirtualProperty]
	#[Serializer\SerializedName('language')]
	#[Serializer\Groups(['User_Entity'])]
//	#[OA\Property(type: 'string')]
	public function getLocale(): string
	{
		return $this->getPreferenceValue(UserPreference::LOCALE, ProfileInterface::DEFAULT_LANGUAGE, false);
	}

	#[Serializer\VirtualProperty]
	#[Serializer\SerializedName('timezone')]
	#[Serializer\Groups(['User_Entity'])]
//	#[OA\Property(type: 'string')]
	public function getTimezone(): string
	{
		return $this->getPreferenceValue(UserPreference::TIMEZONE, date_default_timezone_get(), false);
	}

	public function getLanguage(): string
	{
		return $this->getLocale();
	}

	public function setLanguage(?string $language): void
	{
		if ($language === null) {
			$language = ProfileInterface::DEFAULT_LANGUAGE;
		}
		$this->setPreferenceValue(UserPreference::LOCALE, $language);
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

	public function getReports(): Collection
	{
		return $this->reports;
	}

	public function addReport(ReportInterface $report): self
	{
		if (!$this->reports->contains($report)) {
			$this->reports[] = $report;
			$report->setSender($this);
		}

		return $this;
	}

	public function removeReport(ReportInterface $report): self
	{
		if ($this->reports->contains($report)) {
			$this->reports->removeElement($report);
			if ($report->getSender() === $this) {
				$report->setSender(null);
			}
		}

		return $this;
	}

	public function getAccusations(): Collection
	{
		return $this->accusations;
	}

	public function addAccusation(ReportInterface $accusation): self
	{
		if (!$this->accusations->contains($accusation)) {
			$this->accusations[] = $accusation;
			$accusation->setSender($this);
		}

		return $this;
	}

	public function removeAccusation(ReportInterface $accusation): self
	{
		if ($this->accusations->contains($accusation)) {
			$this->accusations->removeElement($accusation);
			if ($accusation->getSender() === $this) {
				$accusation->setSender(null);
			}
		}

		return $this;
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
