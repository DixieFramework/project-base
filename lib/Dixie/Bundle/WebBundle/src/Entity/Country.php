<?php

declare(strict_types=1);

namespace Talav\WebBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Talav\WebBundle\Repository\CityRepository;
use Talav\WebBundle\Repository\CountryRepository;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ORM\Table('country')]
class Country
{
    public const NAME = 'name';

	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'AUTO')]
	#[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
    protected mixed $id = null;

    #[ORM\Column(name: 'name', type: 'string')]
    protected string $name;

    #[ORM\OneToMany(mappedBy: 'country', targetEntity: Region::class)]
    protected Collection $regions;

    public function __construct()
    {
        $this->regions = new ArrayCollection();
    }

    public function setId(mixed $id): void
    {
        $this->id = $id;
    }

    public function getId(): mixed
    {
        return $this->id;
    }

    public function setName(string $name): Country
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setRegions($regions): self
    {
        $this->regions = $regions;
        return $this;
    }

    public function getRegions(): Collection
    {
        return $this->regions;
    }
}
