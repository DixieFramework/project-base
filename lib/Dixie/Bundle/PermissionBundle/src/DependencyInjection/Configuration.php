<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Talav\Component\Resource\Manager\ResourceManager;
use Talav\PermissionBundle\Entity\Permission;
use Talav\PermissionBundle\Entity\RoleInterface;
use Talav\PermissionBundle\Enum\Role;
use Talav\PermissionBundle\Repository\PermissionRepository;
use Talav\PermissionBundle\Repository\RoleRepository;
use Talav\PermissionBundle\Security\Voter\AccessVoter;

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

		$rootNode
			->children()
                ->append($this->getAccessRolesDefinition())
				->append($this->getPermissionsNode())
			->end()
		->end();

        return $treeBuilder;
    }

    /**
     * Build configuration tree for access roles.
     *
     * @return ArrayNodeDefinition
     */
    protected function getAccessRolesDefinition()
    {
        $node = new ArrayNodeDefinition('security');

        $node
            ->info('Configuration of security voter access roles.')
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')
                    ->info('Enables or disables access controll.')
                    ->defaultTrue()
                ->end()
                ->arrayNode(AccessVoter::VIEW)
                    ->defaultValue(array(Role::MANAGE_RATE, Role::VIEW_RATE))
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode(AccessVoter::CREATE)
                    ->defaultValue(array(Role::MANAGE_RATE, Role::CREATE_RATE))
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode(AccessVoter::EDIT)
                    ->defaultValue(array(Role::MANAGE_RATE, Role::EDIT_RATE))
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode(AccessVoter::DELETE)
                    ->defaultValue(array(Role::MANAGE_RATE, Role::DELETE_RATE))
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ->end();


        return $node;
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
                                        ->scalarNode('model')->defaultValue(RoleInterface::class)->end()
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

	private function getPermissionsNode(): ArrayNodeDefinition
    {
        $builder = new TreeBuilder('permissions');
        /** @var ArrayNodeDefinition $node */
        $node = $builder->getRootNode();

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('sets')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('key')
                    ->arrayPrototype()
                        ->useAttributeAsKey('key')
                        ->isRequired()
                        ->scalarPrototype()->end()
                        ->defaultValue([])
                    ->end()
                ->end()
                ->arrayNode('maps')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('key')
                    ->arrayPrototype()
                        ->useAttributeAsKey('key')
                        ->isRequired()
                        ->scalarPrototype()->end()
                        ->defaultValue([])
                    ->end()
                ->end()
                ->arrayNode('roles')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('key')
                    ->arrayPrototype()
                        ->isRequired()
                        ->scalarPrototype()->end()
                        ->defaultValue([])
                    ->end()
                    ->defaultValue([
                        'ROLE_USER' => [],
                        'ROLE_TEAMLEAD' => [],
                        'ROLE_ADMIN' => [],
                        'ROLE_SUPER_ADMIN' => [],
                    ])
                ->end()
            ->end()
        ;

        return $node;
    }
}
