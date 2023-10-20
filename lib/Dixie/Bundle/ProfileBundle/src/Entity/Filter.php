<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Talav\ProfileBundle\Repository\FilterRepository;

#[ORM\Entity(repositoryClass: FilterRepository::class)]
#[ORM\Table('filter')]
class Filter
{
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: ProfileInterface::class)]
    #[ORM\JoinColumn(name: 'profile_id', referencedColumnName: 'id')]
    protected ProfileInterface $profile;

    #[ORM\OneToOne(targetEntity: Region::class)]
    #[ORM\JoinColumn(name: 'region_id', referencedColumnName: 'id')]
    private ?Region $region;

    /**
     * @ORM\Column(type="integer")
     */
    #[ORM\Column(type: 'integer')]
    private ?int $distance = 100_000;

    /**
     * @ORM\Column(type="integer")
     */
    #[ORM\Column(type: 'integer')]
    private int $minAge = 18;

    /**
     * @ORM\Column(type="integer")
     */
    #[ORM\Column(type: 'integer')]
    private ?int $maxAge = 100;

    public function __construct()
    {
        $this->region = null;
    }

    public function setProfile(ProfileInterface $profile): Filter
    {
        $this->profile = $profile;
        return $this;
    }

    public function getProfile(): ProfileInterface
    {
        return $this->profile;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(?int $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getMinAge(): ?int
    {
        return $this->minAge;
    }

    public function setMinAge(int $minAge): self
    {
        $this->minAge = $minAge;
        return $this;
    }

    public function getMaxAge(): ?int
    {
        return $this->maxAge;
    }

    public function setMaxAge(?int $maxAge): self
    {
        $this->maxAge= $maxAge;
        return $this;
    }
}
