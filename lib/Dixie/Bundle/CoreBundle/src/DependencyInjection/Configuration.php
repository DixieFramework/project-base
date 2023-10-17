<?php

declare(strict_types=1);

namespace Talav\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    final public const ROOT_NODE = 'talav_core';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
	    $treeBuilder = new TreeBuilder(static::ROOT_NODE);

	    // BC layer for symfony/config 4.1 and older
	    if (! \method_exists($treeBuilder, 'getRootNode')) {
		    $rootNode = $treeBuilder->root(static::ROOT_NODE);
	    } else {
		    $rootNode = $treeBuilder->getRootNode();
	    }

	    $rootNode->addDefaultsIfNotSet();

		$rootNode
			->children()
                ->arrayNode('bundles')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('search_paths')
                            ->prototype('scalar')->end()
                        ->end()
                        ->booleanNode('handle_composer')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
			->end()
		->end();

        return $treeBuilder;
    }
}
