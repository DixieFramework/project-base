<?php

declare(strict_types=1);

namespace Talav\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Country.
 *
 *
 * @SuppressWarnings(PHPMD)
 * Auto generated class do not check mess
 */
#[ORM\Table(name: 'geo_names_countries')]
#[ORM\Entity]
class GeoCountry
{
    /**
     * @var int
     *
     *
     */
    #[ORM\Column(name: 'geonameId', type: 'integer', nullable: true)]
    #[Groups(['Member:Read'])]
    private $geonameId;

    /**
     * @var string
     *
     *
     */
    #[ORM\Column(name: 'name', type: 'string', length: 200, nullable: true)]
    #[Groups(['Member:Read'])]
    private $name;

    /**
     * @var string
     *
     *
     */
    #[ORM\Column(name: 'continent', type: 'string', length: 2, nullable: true)]
    #[Groups(['Member:Read'])]
    private $continent;

    /**
     * @var string
     */
    #[ORM\Column(name: 'country', type: 'string', length: 2)]
    #[ORM\Id]
    private $country;

    /**
     * Set geonameId.
     *
     * @param int $geonameId
     *
     * @return GeoCountry
     */
    public function setGeonameId($geonameId)
    {
        $this->geonameId = $geonameId;

        return $this;
    }

    /**
     * Get geonameId.
     *
     * @return int
     */
    public function getGeonameId()
    {
        return $this->geonameId;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return GeoCountry
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set continent.
     *
     * @param string $continent
     *
     * @return GeoCountry
     */
    public function setContinent($continent)
    {
        $this->continent = $continent;

        return $this;
    }

    /**
     * Get continent.
     *
     * @return string
     */
    public function getContinent()
    {
        return $this->continent;
    }

    /**
     * Get country.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set country.
     *
     * @param string $country
     *
     * @return string
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }
}
