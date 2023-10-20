<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Talav\ProfileBundle\Repository\AttributeCategoryRepository;

#[ORM\Entity(repositoryClass: AttributeCategoryRepository::class)]
#[ORM\Table('attribute_category')]
class AttributeCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    protected ?int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    protected string $name;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Attribute::class)]
    protected Collection $attributes;

    public function __construct()
    {
        $this->attributes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getLowercaseName(): ?string
    {
        return strtolower($this->name);
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setAttributes($attributes): void
    {
        $this->attributes = $attributes;
    }

    public function getAttributes(): Collection
    {
        return $this->attributes;
    }
}
