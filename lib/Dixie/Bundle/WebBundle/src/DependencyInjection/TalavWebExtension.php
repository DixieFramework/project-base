<?php

declare(strict_types=1);

namespace Talav\WebBundle\DependencyInjection;

use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class TalavWebExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $locator = new FileLocator(__DIR__ . '/../Resources/config');

        $loader = new DelegatingLoader(
            new LoaderResolver(
                [
                    new Loader\GlobFileLoader($container, $locator),
                    new Loader\YamlFileLoader($container, $locator),
                    new Loader\XmlFileLoader($container, $locator),
                ],
            ),
        );

        $loader->load('services.yml');
//        $loader->load('components.xml');


//        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
//	    $loader->load('services.yml');

	    $config = $this->processConfiguration(new Configuration(), $configs);
	    $container->setParameter('talav_web.configuration', $config);
    }
}
