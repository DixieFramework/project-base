<?php

declare(strict_types=1);

namespace Talav\CoreBundle\HttpKernel\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

abstract class AbstractTalavBundle extends Bundle implements TalavBundleInterface
{
    protected static ?TalavBundleManager $bundleManager = null;

    public function getNiceName(): string
    {
        return $this->getName();
    }

    public function getDescription(): string
    {
        return '';
    }

    public function getVersion(): string
    {
        return '';
    }

    public function getInstaller(): ?Installer\InstallerInterface
    {
        return null;
    }

    public static function isInstalled(): bool
    {
        static::$bundleManager ??= \Pimcore::getContainer()->get(PimcoreBundleManager::class);

        $bundle = static::$bundleManager->getActiveBundle(static::class, false);

        return static::$bundleManager->isInstalled($bundle);
    }
}
