<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

/**
 * Trait for class implementing {@link DisableListenerInterface DisableListenerInterface} interface.
 *
 * @psalm-require-implements \App\Interfaces\DisableListenerInterface
 */
trait DisableListenerTrait
{
    /**
     * The enabled state.
     */
    private bool $enabled = true;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }
}
