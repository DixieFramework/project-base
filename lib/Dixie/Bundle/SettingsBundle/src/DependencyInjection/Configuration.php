<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Talav\SettingsBundle\Manager\SettingsManager;
use Talav\SettingsBundle\Entity\Settings;
use Talav\SettingsBundle\Repository\SettingsRepository;

final class Configuration implements ConfigurationInterface
{
    const ROOT_NODE = 'talav_settings';

    /**
     * {@inheritdoc}
     * @return TreeBuilder
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

        $this->addResourceSection($treeBuilder->getRootNode());

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('settings')
                    ->useAttributeAsKey('settings')
                    ->prototype('array')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('scope')->defaultValue('global')->end()
                            ->scalarNode('value')
                                ->beforeNormalization()
                                ->ifArray()
                                ->then(function ($value) {
                                    return json_encode($value);
                                })
                                ->end()
                                ->defaultValue(null)
                            ->end()
                            ->scalarNode('type')
                                ->defaultValue('string')
                                ->validate()
                                    ->always(function ($v) {
                                        if (!in_array($v, ['string', 'array', 'boolean'])) {
                                            throw new InvalidTypeException();
                                        }

                                        return $v;
                                    })
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function addResourceSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('settings')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Settings::class)->end()
                                        ->scalarNode('repository')->defaultValue(SettingsRepository::class)->end()
                                        ->scalarNode('manager')->defaultValue(SettingsManager::class)->end()
	                                ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
