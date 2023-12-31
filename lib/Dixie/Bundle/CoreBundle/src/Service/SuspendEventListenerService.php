<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Service;

use Talav\CoreBundle\Interfaces\DisableListenerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

/**
 * Service to enable or disable listeners. Only listeners implementing
 * the <code>DisableListenerInterface</code> interface are taken into account.
 *
 * @see DisableListenerInterface
 */
class SuspendEventListenerService
{
    /**
     * The disabled state.
     */
    private bool $disabled = false;

    /**
     * Constructor.
     *
     * @param iterable<DisableListenerInterface> $listeners
     */
    public function __construct(
        #[TaggedIterator(DisableListenerInterface::class)]
        private readonly iterable $listeners
    ) {
    }

    /**
     * Destructor. The listeners are automatically enabled.
     */
    public function __destruct()
    {
        $this->enableListeners();
    }

    /**
     * Disable listeners. Do nothing if the listeners are already disabled.
     */
    public function disableListeners(): void
    {
        if (!$this->disabled) {
            $this->updateListeners(false);
            $this->disabled = true;
        }
    }

    /**
     * Enable listeners. Do nothing if the listeners are not disabled.
     */
    public function enableListeners(): void
    {
        if ($this->disabled) {
            $this->updateListeners(true);
            $this->disabled = false;
        }
    }

    /**
     * Returns a value indicating if the listeners are disabled.
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * Update listeners enablement.
     */
    private function updateListeners(bool $enabled): void
    {
        foreach ($this->listeners as $listener) {
            $listener->setEnabled($enabled);
        }
    }
}
