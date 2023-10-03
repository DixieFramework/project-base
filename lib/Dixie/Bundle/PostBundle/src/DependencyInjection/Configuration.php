<?php

declare(strict_types=1);

namespace Talav\PostBundle\DependencyInjection;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Talav\PostBundle\Entity\Comment;
use Talav\PostBundle\Entity\Post;
use Talav\PostBundle\Repository\CommentRepository;
use Talav\PostBundle\Repository\PostRepository;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    final public const ROOT_NODE = 'talav_post';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
	    $treeBuilder = new TreeBuilder(static::ROOT_NODE);

	    // BC layer for symfony/config 4.1 and older
	    if (! \method_exists($treeBuilder, 'getRootNode')) {
		    $rootNode = $treeBuilder->root(static::ROOT_NODE);
	    } else {
		    $rootNode = $treeBuilder->getRootNode();
	    }

        $this->addResourceSection($rootNode);

        return $treeBuilder;
    }

    private function addResourceSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('post')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Post::class)->end()
                                        ->scalarNode('repository')->defaultValue(PostRepository::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('post_comment')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Comment::class)->end()
                                        ->scalarNode('repository')->defaultValue(CommentRepository::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
//                        ->arrayNode('user_friend')
//                            ->addDefaultsIfNotSet()
//                            ->children()
//                                ->arrayNode('classes')
//                                    ->addDefaultsIfNotSet()
//                                    ->children()
//                                        ->scalarNode('model')->defaultValue(UserFriend::class)->end()
//                                        ->scalarNode('repository')->defaultValue(UserFriendRepository::class)->end()
//                                    ->end()
//                                ->end()
//                            ->end()
//                        ->end()
//                        ->arrayNode('friendship')
//                            ->addDefaultsIfNotSet()
//                            ->children()
//                                ->arrayNode('classes')
//                                    ->addDefaultsIfNotSet()
//                                    ->children()
//                                        ->scalarNode('model')->defaultValue(Friendship::class)->end()
//                                        ->scalarNode('repository')->defaultValue(FriendshipRepository::class)->end()
//                                    ->end()
//                                ->end()
//                            ->end()
//                        ->end()
//                        ->arrayNode('friendship_request')
//                            ->addDefaultsIfNotSet()
//                            ->children()
//                                ->arrayNode('classes')
//                                    ->addDefaultsIfNotSet()
//                                    ->children()
//                                        ->scalarNode('model')->defaultValue(FriendshipRequest::class)->end()
//                                        ->scalarNode('repository')->defaultValue(FriendshipRequestRepository::class)->end()
//                                    ->end()
//                                ->end()
//                            ->end()
//                        ->end()
//                        ->arrayNode('notification')
//                            ->addDefaultsIfNotSet()
//                            ->children()
//                                ->arrayNode('classes')
//                                    ->addDefaultsIfNotSet()
//                                    ->children()
//                                        ->scalarNode('model')->defaultValue(Notification::class)->end()
//                                        ->scalarNode('repository')->defaultValue(NotificationRepository::class)->end()
//                                    ->end()
//                                ->end()
//                            ->end()
//                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
