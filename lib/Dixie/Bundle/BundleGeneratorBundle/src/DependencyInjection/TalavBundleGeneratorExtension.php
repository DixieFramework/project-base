<?php

declare(strict_types=1);

namespace Talav\BundleGeneratorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Talav\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;

final class TalavBundleGeneratorExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // Load services.
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }
}
