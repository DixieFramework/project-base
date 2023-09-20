<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait ExpiresAtTrait
{
    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    protected ?\DateTimeImmutable $expires_at = null;

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expires_at;
    }

    public function setExpiresAt(?\DateTimeImmutable $expires_at): self
    {
        $this->expires_at = $expires_at;

        return $this;
    }
}
