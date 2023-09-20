<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Talav\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;

final class TalavSettingsExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        // Load services.
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $container->setParameter('talav_settings.settings', $config['settings']);

        $loader->load('services.yml');
        $loader->load('user_scope.yml');

        $this->registerResources('app', $config['resources'], $container);
    }
}
