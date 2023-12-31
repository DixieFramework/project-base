<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait ContentTrait
{
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $content = null;

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content === null ? null : trim($content);

        return $this;
    }
}
