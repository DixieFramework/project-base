<?php

declare(strict_types=1);

namespace Talav\CoreBundle\HttpKernel\Bundle;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Talav\CoreBundle\Utils\ClassUtils;
use Talav\CoreBundle\Utils\Composer;

/**
 * @internal
 */
class TalavBundleLocator
{
    private Composer\PackageInfo $composerPackageInfo;

    private array $paths = [];

    private bool $handleComposer = true;

    public function __construct(Composer\PackageInfo $composerPackageInfo, array $paths = [], bool $handleComposer = true)
    {
        $this->setPaths($paths);

        $this->composerPackageInfo = $composerPackageInfo;
        $this->handleComposer = $handleComposer;
    }

    private function setPaths(array $paths): void
    {
        $fs = new Filesystem();

        foreach ($paths as $path) {
            if (!$fs->isAbsolutePath($path)) {
                $path = DIXIE_PROJECT_ROOT . '/' . $path;
            }

            if ($fs->exists($path)) {
                $this->paths[] = $path;
            }
        }
    }

    /**
     * Locate pimcore bundles in configured paths
     *
     * @return array A list of found bundle class names
     */
    public function findBundles(): array
    {
        $result = $this->findBundlesInPaths($this->paths);
        if ($this->handleComposer) {
            $result = array_merge($result, $this->findComposerBundles());
        }

        $result = array_values($result);
        sort($result);

        return $result;
    }

    private function findBundlesInPaths(array $paths): array
    {
        $result = [];

        $finder = new Finder();
        $finder
            ->in(array_unique(array_filter($paths, 'is_dir')))
	        ->depth('> 1')
	        //->directories();
            ->name('*Bundle.php');
dd($finder->count());
        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
			dd($file);
            $className = ClassUtils::findClassName($file);
            if ($className) {
                $this->processBundleClass($className, $result);
            }
        }

        return $result;
    }

    /**
     * Finds composer bundles in /vendor with the following prerequisites:
     *
     *  * Composer package type is "pimcore-bundle"
     *  * If the [ extra: [ pimcore: [ bundles: [] ] ] entry is available in the config, it will use this config
     *    as list of available bundle names
     *  * If the config entry above is not available, it will scan the package directory with the same logic as for
     *    the other paths
     */
    private function findComposerBundles(): array
    {
        $pimcoreBundles = $this->composerPackageInfo->getInstalledPackages('pimcore-bundle');
        $composerPaths = [];

        $result = [];
        foreach ($pimcoreBundles as $packageInfo) {
            // if bundle explicitly defines bundles, use the config
            if (isset($packageInfo['extra']['dixie'])) {
                $cfg = $packageInfo['extra']['dixie'];
                if (isset($cfg['bundles']) && is_array($cfg['bundles'])) {
                    foreach ($cfg['bundles'] as $bundle) {
                        $this->processBundleClass($bundle, $result);
                    }
                }
            } else {
                // add path to list of composer paths which will be processed via path search
                $composerPaths[] = DIXIE_COMPOSER_PATH . '/' . $packageInfo['name'];
            }
        }

        // wildcard process composer paths which didn't have a dedicated bundle config entry
        if (count($composerPaths) > 0) {
            $result = array_merge($result, $this->findBundlesInPaths($composerPaths));
        }

        return $result;
    }

    private function processBundleClass(string $bundle, array &$result): void
    {
        if (!$bundle) {
            return;
        }

        if (!class_exists($bundle)) {
            return;
        }

        $reflector = new \ReflectionClass($bundle);
        if (!$reflector->isInstantiable() || !$reflector->implementsInterface(TalavBundleInterface::class)) {
            return;
        }

        $result[$reflector->getName()] = $reflector->getName();
    }
}
