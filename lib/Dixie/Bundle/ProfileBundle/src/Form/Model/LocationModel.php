<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Form\Model;

use Talav\WebBundle\Entity\NewLocation;

class LocationModel
{
    public string $name;
    public int $geonameId;
    public float $latitude;
    public float $longitude;

    public function __construct(NewLocation $location = null)
    {
        if (null !== $location) {
            $this->name = $location->getFullName();
            $this->geonameId = $location->getGeonameId();
            $this->latitude = $location->getLatitude();
            $this->longitude = $location->getLongitude();
        }
    }
}
