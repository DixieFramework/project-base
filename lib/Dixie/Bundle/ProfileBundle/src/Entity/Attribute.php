<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Talav\ProfileBundle\Repository\AttributeRepository;

#[ORM\Entity(repositoryClass: AttributeRepository::class)]
#[ORM\Table('attribute')]
class Attribute
{
    final public const NAME = 'name';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    protected ?int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    protected string $name;

    #[ORM\ManyToOne(targetEntity: AttributeCategory::class, inversedBy: 'attributes')]
    #[ORM\JoinColumn(name: 'attribute_category_id', referencedColumnName: 'id')]
    protected AttributeCategory $category;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }
}
