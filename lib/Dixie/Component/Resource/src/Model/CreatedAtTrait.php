<?php

declare(strict_types=1);

namespace Talav\Component\Resource\Model;

use DateTimeImmutable;
use DateTimeInterface;

trait CreatedAtTrait
{
    protected ?DateTimeImmutable $createdAt = null;

    public function setCreatedAtWithCurrentTime(): void
    {
        if (null !== $this->createdAt) {
            $this->createdAt = new DateTimeImmutable();
        }
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface|string|null $createdAt): void
    {
        $this->createdAt = $this->createDateTime($createdAt);
    }
}
