<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Form\Model;

use Talav\WebBundle\Entity\Region;

class ProfileFilterFormModel
{
    private array $colors;
    private array $shapes;
    private ?Region $region;
    private array $interests;
    private ?int $distance = 100_000;
    private ?int $minAge;
    private ?int $maxAge;

    public function __construct()
    {
        $this->region = null;
    }

    public function getShapes(): array
    {
        return $this->shapes;
    }

    public function setShapes(array $shapes): void
    {
        $this->shapes = $shapes;
    }

    public function getColors(): array
    {
        return $this->colors;
    }

    public function setColors(array $colors): void
    {
        $this->colors = $colors;
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

    public function setMinAge(int $minAge): void
    {
        $this->minAge = $minAge;
    }

    public function getMaxAge(): ?int
    {
        return $this->maxAge;
    }

    public function setMaxAge(?int $maxAge): void
    {
        $this->maxAge= $maxAge;
    }

    public function setInterests(array $interests): void
    {
        $this->interests = $interests;
    }

    public function getInterests(): array
    {
        return $this->interests;
    }
}
