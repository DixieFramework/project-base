<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Entity\Interfaces;

interface UuidIdentifiableInterface extends BaseIdentifiableInterface
{
    /**
     * Get ID of entity.
     */
    public function getId(): ?string;

    /**
     * Set ID of entity.
     */
    public function setId(?string $id): static;
}
