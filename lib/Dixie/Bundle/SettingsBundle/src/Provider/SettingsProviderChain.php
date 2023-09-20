<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Provider;

final class SettingsProviderChain implements SettingsProviderInterface
{
    /**
     * @var array
     */
    private $providers = [];

    /**
     * SettingsProviderChain constructor.
     *
     * @param array $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * {@inheritdoc}
     */
    public function getSettings(): array
    {
        $settings = [];
        /** @var SettingsProviderInterface $provider */
        foreach ($this->providers as $provider) {
            if ($provider->supports()) {
                $settings += $provider->getSettings();
            }
        }

        return $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(): bool
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports()) {
                return true;
            }
        }

        return false;
    }
}
