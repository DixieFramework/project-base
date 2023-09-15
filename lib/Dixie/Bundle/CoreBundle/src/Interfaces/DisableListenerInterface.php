<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Interfaces;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Class implementing this interface deals with enablement state.
 */
#[AutoconfigureTag]
interface DisableListenerInterface
{
    /**
     * Gets the enabled state.
     *
     * @return bool true if enabled; false if disabled
     */
    public function isEnabled(): bool;

    /**
     * Sets the enabled state.
     *
     * @param bool $enabled true to enable; false to disable
     */
    public function setEnabled(bool $enabled): static;
}
