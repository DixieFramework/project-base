<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Talav\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;

final class TalavPermissionExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // Load services.
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $config = $this->processConfiguration(new Configuration(), $configs);

        if (isset($config['permissions'])) {
            $container
                ->getDefinition('talav_permission.security.config_permission_loader')
                ->replaceArgument(0, $config['permissions'] ?? []);
        }


        $this->registerResources('app', $config['resources'], $container);
    }
}
