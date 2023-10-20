<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait DescriptionTrait
{
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description === null ? null : trim($description);

        return $this;
    }
}
