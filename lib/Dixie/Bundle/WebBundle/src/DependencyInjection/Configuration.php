<?php

declare(strict_types=1);

namespace Talav\WebBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
	    $tree = new TreeBuilder('talav_web');
	    $root = $tree->getRootNode();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

	    $this->addApplicationSection($root);

	    return $tree;
    }

	private function addApplicationSection(ArrayNodeDefinition|NodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode('application')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('name')->defaultValue('Devscast Dashboard')->end()
                        ->scalarNode('title')->defaultValue('devscast.org')->end()
                        ->scalarNode('logo_path')->isRequired()->end()
                        ->scalarNode('icon_path')->isRequired()->end()
                        ->scalarNode('version')->defaultValue('1.0.0')->end()
                        ->scalarNode('copyrights')->defaultValue('')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
