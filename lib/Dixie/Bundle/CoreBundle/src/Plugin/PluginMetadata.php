<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Plugin;

use Talav\CoreBundle\Constants\ApplicationConstants;

class PluginMetadata
{
    private ?string $version = null;
    private ?int $kimaiVersion = null;
    private ?string $homepage = null;
    private ?string $description = null;
    private ?string $name = null;

    /**
     * @param string $path
     * @return PluginMetadata
     * @throws \Exception
     */
    public static function loadFromComposer(string $path): PluginMetadata
    {
        if (!is_dir($path) || !is_readable($path)) {
            throw new \Exception(sprintf('Bundle directory "%s" cannot be accessed.', $path));
        }

        $pluginName = basename($path);
        $composer = $path . '/composer.json';

        if (!file_exists($composer) || !is_readable($composer)) {
            throw new \Exception(sprintf('Bundle "%s" does not ship composer.json, which is required since 2.0.', $pluginName));
        }

        $json = json_decode(file_get_contents($composer), true);

        if (!\array_key_exists('extra', $json)) {
            throw new \Exception(sprintf('Bundle "%s" does not define an "extra" node in composer.json, which is required since 2.0.', $pluginName));
        }

        if (!\array_key_exists('kimai', $json['extra'])) {
            throw new \Exception(sprintf('Bundle "%s" does not define the "extra.kimai" node in composer.json, which is required since 2.0.', $pluginName));
        }

        if (!\array_key_exists('require', $json['extra']['kimai'])) {
            throw new \Exception(sprintf('Bundle "%s" does not define the minimum Kimai version in "extra.kimai.required" in composer.json, which is required since 2.0.', $pluginName));
        }

        if (!\array_key_exists('name', $json['extra']['kimai'])) {
            throw new \Exception(sprintf('Bundle "%s" does not define its name in "extra.kimai.name" in composer.json, which is required since 2.0.', $pluginName));
        }

        if (!\is_int($json['extra']['kimai']['require'])) {
            throw new \Exception(sprintf('Bundle "%s" defines an invalid Kimai minimum version in extra.kimai.require. Please provide an integer as in Constants::VERSION_ID.', $pluginName));
        }

        $meta = new self();
        $meta->description = $json['description'] ?? '';
        $meta->homepage = $json['homepage'] ?? Constants::HOMEPAGE . '/store/';
        $meta->name = $json['extra']['kimai']['name'];
        $meta->kimaiVersion = $json['extra']['kimai']['require'];

        // the version field is required if we use composer to install a plugin via var/packages/
        $meta->version = $json['extra']['kimai']['version'] ?? ($json['version'] ?? 'unknown');

        return $meta;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function getKimaiVersion(): ?int
    {
        return $this->kimaiVersion;
    }

    public function getHomepage(): ?string
    {
        return $this->homepage;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
