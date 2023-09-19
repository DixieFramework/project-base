<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Talav\Component\Resource\Manager\ResourceManager;
use Talav\PermissionBundle\Entity\Permission;
use Talav\PermissionBundle\Entity\Role;
use Talav\PermissionBundle\Repository\PermissionRepository;
use Talav\PermissionBundle\Repository\RoleRepository;

final class Configuration implements ConfigurationInterface
{
    final public const ROOT_NODE = 'talav_permission';

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

        $this->addResourceSection($treeBuilder->getRootNode());

        return $treeBuilder;
    }

    private function addResourceSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('role')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Role::class)->end()
                                        ->scalarNode('repository')->defaultValue(RoleRepository::class)->end()
                                        ->scalarNode('manager')->defaultValue(ResourceManager::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('permission')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Permission::class)->end()
                                        ->scalarNode('repository')->defaultValue(PermissionRepository::class)->end()
                                        ->scalarNode('manager')->defaultValue(ResourceManager::class)->end()
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
