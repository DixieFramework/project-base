<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\Resource\Model\Timestampable;
use Talav\Component\User\Model\UserInterface;
use Talav\ProfileBundle\Enum\Gender;
use Talav\ProfileBundle\Model\ProfileInterface;

class Profile implements ProfileInterface
{
    use ResourceTrait;
    use Timestampable;

    protected ?string $firstName = null;

    protected ?string $lastName = null;

//    #[ORM\Column(type: 'string', enumType: Gender::class)]
    protected Gender $gender;

    protected ?\DateTimeInterface $birthdate = null;

    protected UserInterface $user;

	protected Collection $requester;

	protected Collection $requestee;

	protected Collection $friendships;

    /**
     * @var ArrayCollection
     */
    private readonly Collection $relationships;

    public function __construct()
    {
	    $this->requester = new ArrayCollection();
	    $this->requestee = new ArrayCollection();
	    $this->friendships = new ArrayCollection();
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

    public function getRelationships(): Collection
    {
        return $this->relationships;
    }
}
