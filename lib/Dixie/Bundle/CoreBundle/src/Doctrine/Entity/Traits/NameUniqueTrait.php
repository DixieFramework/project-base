<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait NameUniqueTrait
{
    
    #[ORM\Column(type: Types::STRING, length: 255, nullable: false, unique: true)]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', length: 255, nullable: false, unique: true)]
    #[Assert\Length(max: 255)]
    protected string $name;

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = trim((string) $name);

        return $this;
    }
}
