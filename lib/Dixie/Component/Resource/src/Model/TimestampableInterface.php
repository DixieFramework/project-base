<?php

declare(strict_types=1);

namespace Talav\Component\Resource\Model;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * TimestampableInterface should be implemented by classes which needs to be
 * identified as Timestampable.
 */
interface TimestampableInterface
{
    /**
     * Gets the created at datetime.
     *
     * @return ?DateTimeImmutable The DateTime instance
     */
    public function getCreatedAt(): ?DateTimeImmutable;

    /**
     * Sets the created at datetime.
     *
     * @param DateTimeInterface|string|null $createdAt The DateTime instance
     */
    public function setCreatedAt(DateTimeInterface|string|null $createdAt): void;

    /**
     * Gets the updated at datetime.
     *
     * @return ?DateTimeImmutable The DateTime instance
     */
    public function getUpdatedAt(): ?DateTimeImmutable;

    /**
     * Sets the updated at datetime.
     *
     * @param DateTimeInterface|string|null $updatedAt The DateTime instance
     */
    public function setUpdatedAt(DateTimeInterface|string|null $updatedAt): void;
}
