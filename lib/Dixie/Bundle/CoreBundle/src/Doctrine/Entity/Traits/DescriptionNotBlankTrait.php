<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait DescriptionNotBlankTrait
{
    
    #[ORM\Column(type: Types::TEXT, nullable: false)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[ORM\Column(type: 'text', nullable: false)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    protected string $description = '';

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = trim((string) $description);

        return $this;
    }
}
