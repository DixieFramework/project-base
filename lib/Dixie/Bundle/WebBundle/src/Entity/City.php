<?php

declare(strict_types=1);

namespace Talav\WebBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Symfony\Component\Serializer\Annotation\Groups;
use Talav\WebBundle\Repository\CityRepository;

#[ORM\Entity(repositoryClass: CityRepository::class)]
#[ORM\Table('city')]
class City
{
    public const NAME = 'name';

//    /**
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
//    #[Groups('serialize')]
    protected mixed $id = null;

    #[ORM\ManyToOne(targetEntity: Region::class, inversedBy: 'cities')]
    #[ORM\JoinColumn(name: 'region_id', referencedColumnName: 'id')]
    protected Region $region;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(name: 'country_id', referencedColumnName: 'id')]
    protected Country $country;

    #[ORM\Column(type: 'float')]
    protected float $longitude;

    #[ORM\Column(type: 'float')]
    protected float $latitude;

	#[ORM\Column(type: 'string')]
	protected string $municipality;

	#[ORM\Column(type: 'integer')]
	protected int $zip;

	#[ORM\Column(type: 'string')]
    protected string $name;

    public function getId(): mixed
    {
        return $this->id;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(Region $region): City
    {
        $this->region = $region;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): City
    {
        $this->country = $country;

        return $this;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): City
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): City
    {
        $this->latitude = $latitude;

        return $this;
    }

	/**
	 * @return string
	 */
	public function getMunicipality(): string
	{
		return $this->municipality;
	}

	/**
	 * @param string $municipality
	 */
	public function setMunicipality(string $municipality): City
	{
		$this->municipality = $municipality;

		return $this;
	}

	public function getZip(): int
	{
		return $this->zip;
	}

	public function setZip(int $zip): City
	{
		$this->zip = $zip;

		return $this;
	}

    #[Groups('serialize')]
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): City
    {
        $this->name = $name;

        return $this;
    }

    public function setId(mixed $id): void
    {
        $this->id = $id;
    }
}
