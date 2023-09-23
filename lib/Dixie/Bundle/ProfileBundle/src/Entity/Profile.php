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

class Profile implements ProfileInterface
{
    use ResourceTrait;
    use Timestampable;

    protected ?string $firstName = null;

    protected ?string $lastName = null;

//    #[ORM\Column(type: 'string', enumType: Gender::class)]
    protected Gender $gender;
    protected UserInterface $user;

    /**
     * @var ArrayCollection
     */
    private readonly Collection $relationships;

    public function __construct()
    {
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
        $this->user = $user;

        return $this;
    }

    public function getRelationships(): Collection
    {
        return $this->relationships;
    }
}
