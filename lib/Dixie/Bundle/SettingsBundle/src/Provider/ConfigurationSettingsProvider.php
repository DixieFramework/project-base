<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Provider;

final class ConfigurationSettingsProvider implements SettingsProviderInterface
{
    /**
     * @var array
     */
    private $settingsConfiguration = [];

    /**
     * ConfigurationSettingsProvider constructor.
     *
     * @param array $settingsConfiguration
     */
    public function __construct(array $settingsConfiguration = [])
    {
        $this->settingsConfiguration = $settingsConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getSettings(): array
    {
        return $this->settingsConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(): bool
    {
        return true;
    }
}
