<?php

declare(strict_types=1);

namespace PermissionAppBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Talav\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;

final class PermissionAppExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // Load services.
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
