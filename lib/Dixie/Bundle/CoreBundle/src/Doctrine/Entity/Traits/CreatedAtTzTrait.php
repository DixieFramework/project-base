<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait CreatedAtTzTrait
{
    #[ORM\Column(name: 'created_at', type: Types::DATETIMETZ_IMMUTABLE)]
    protected ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('@'.time());
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt?->setTimezone(new \DateTimeZone(date_default_timezone_get()));
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        if (!$createdAt) {
            $createdAt = new \DateTimeImmutable('@'.time());
        }

        $this->createdAt = $createdAt;

        return $this;
    }
}
