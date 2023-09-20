<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Tests\Functional\app;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        if (!file_exists($filename = $this->getProjectDir().'/Tests/Functional/app/SettingsBundle/bundles.php')) {
            throw new \RuntimeException(sprintf('The bundles file "%s" does not exist.', $filename));
        }

        return include $filename;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/SettingsBundle/config.yml');
    }

    protected function getKernelParameters()
    {
        $parameters = parent::getKernelParameters();

        return $parameters;
    }
}
