<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Utils\Composer;

/**
 * @internal
 */
class PackageInfo
{
    private ?array $installedPackages = null;

    /**
     * Gets installed packages, optionally filtered by type
     *
     * @param array|string|null $type
     *
     * @return array
     */
    public function getInstalledPackages(array|string $type = null): array
    {
        $packages = $this->readInstalledPackages();

        if (null !== $type) {
            if (!is_array($type)) {
                $type = [$type];
            }

            $packages = array_filter($packages, static fn (array $package) => in_array($package['type'], $type, true));
        }

        return $packages;
    }

    private function readInstalledPackages(): array
    {
        if (null !== $this->installedPackages) {
            return $this->installedPackages;
        }

        $json = $this->readComposerFile(DIXIE_COMPOSER_PATH . '/composer/installed.json');
        if ($json && is_array($json)) {
            return $this->installedPackages = $json['packages'] ?? $json;
        }

        return $this->installedPackages = [];
    }

    private function readComposerFile(string $path): ?array
    {
        if (is_file($path) && is_readable($path)) {
            try {
                return json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new \RuntimeException(sprintf('Failed to parse composer file %s', $path), previous: $e);
            }
        }

        return null;
    }
}
