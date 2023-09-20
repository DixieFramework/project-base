<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait CreatedAtTzTrait
{
    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    protected ?\DateTimeImmutable $created_at = null;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at?->setTimezone(new \DateTimeZone(date_default_timezone_get()));
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
