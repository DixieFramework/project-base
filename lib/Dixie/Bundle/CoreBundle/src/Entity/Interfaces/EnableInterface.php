<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Entity\Interfaces;

interface EnableInterface
{
    /**
     * Get enabled state of entity.
     */
    public function isEnabled(): bool;

    /**
     * Check if entity is in disabled state.
     */
    public function isDisabled(): bool;

    /**
     * Set enabled state of entity.
     */
    public function setEnabled(bool $enabled): static;
}
