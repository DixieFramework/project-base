<?php

declare(strict_types=1);

namespace Talav\Component\Resource\Model;

use DateTimeImmutable;
use DateTimeInterface;

trait UpdatedAtTrait
{
    protected ?DateTimeImmutable $updatedAt = null;

    public function setUpdatedAtWithCurrentTime(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface|string|null $updatedAt): void
    {
        $this->updatedAt = $this->createDateTime($updatedAt);
    }
}
