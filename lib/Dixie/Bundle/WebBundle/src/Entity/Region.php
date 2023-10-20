<?php

declare(strict_types=1);

namespace Talav\WebBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Serializer\Annotation\Groups;
use Talav\WebBundle\Repository\RegionRepository;

#[ORM\Entity(repositoryClass: RegionRepository::class)]
#[ORM\Table('region')]
class Region
{
    public const NAME = 'name';

//    /**
//     *
//     * @ORM\Id()
//     * @ORM\GeneratedValue(strategy="CUSTOM")
//     * @ORM\CustomIdGenerator("doctrine.uuid_generator")
//     * @ORM\Column(type="uuid")
//     * @Groups("serialize")
//     */
//    private Uuid $id;
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'AUTO')]
	#[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
    #[Groups('serialize')]
    protected mixed $id = null;

    #[ORM\Column(name: 'name', type: 'string')]
    #[Groups('serialize')]
    protected string $name;

    #[ORM\ManyToOne(targetEntity: Country::class, inversedBy: 'regions')]
    #[ORM\JoinColumn(name: 'country_id', referencedColumnName: 'id')]
    protected Country $country;

    #[ORM\OneToMany(mappedBy: 'region', targetEntity: City::class)]
    protected Collection $cities;

    public function __construct()
    {
        $this->cities = new ArrayCollection();
    }

    public function getId(): mixed
    {
        return $this->id;
    }

    public function setCountry(Country $country): Region
    {
        $this->country = $country;
        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setId(mixed $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): Region
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function setCities($cities): void
    {
        $this->cities = $cities;
    }
}
