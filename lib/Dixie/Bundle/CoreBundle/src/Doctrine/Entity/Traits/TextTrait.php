<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait TextTrait
{
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $text = null;

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = ($text === null) ? null : trim($text);

        return $this;
    }
}
