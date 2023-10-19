<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\DependencyInjection;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Talav\ProfileBundle\Entity\Friendship;
use Talav\ProfileBundle\Entity\FriendshipRequest;
use Talav\ProfileBundle\Entity\Like;
use Talav\ProfileBundle\Entity\Message;
use Talav\ProfileBundle\Entity\Notification;
use Talav\ProfileBundle\Entity\Profile;
use Talav\ProfileBundle\Entity\Report;
use Talav\ProfileBundle\Entity\UserFriend;
use Talav\ProfileBundle\Entity\UserProperty;
use Talav\ProfileBundle\Entity\UserRelation;
use Talav\ProfileBundle\Repository\FriendshipRepository;
use Talav\ProfileBundle\Repository\FriendshipRequestRepository;
use Talav\ProfileBundle\Repository\LikeRepository;
use Talav\ProfileBundle\Repository\MessageRepository;
use Talav\ProfileBundle\Repository\NotificationRepository;
use Talav\ProfileBundle\Repository\ProfileRepository;
use Talav\ProfileBundle\Repository\ReportRepository;
use Talav\ProfileBundle\Repository\UserFriendRepository;
use Talav\ProfileBundle\Repository\UserPropertyRepository;
use Talav\ProfileBundle\Repository\UserRelationRepository;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    final public const ROOT_NODE = 'talav_profile';

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
                        ->arrayNode('profile')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Profile::class)->end()
                                        ->scalarNode('repository')->defaultValue(ProfileRepository::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('user_relation')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(UserRelation::class)->end()
                                        ->scalarNode('repository')->defaultValue(UserRelationRepository::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('user_friend')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(UserFriend::class)->end()
                                        ->scalarNode('repository')->defaultValue(UserFriendRepository::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('friendship')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Friendship::class)->end()
                                        ->scalarNode('repository')->defaultValue(FriendshipRepository::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('friendship_request')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(FriendshipRequest::class)->end()
                                        ->scalarNode('repository')->defaultValue(FriendshipRequestRepository::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('notification')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Notification::class)->end()
                                        ->scalarNode('repository')->defaultValue(NotificationRepository::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('message')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Message::class)->end()
                                        ->scalarNode('repository')->defaultValue(MessageRepository::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('like')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Like::class)->end()
                                        ->scalarNode('repository')->defaultValue(LikeRepository::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('report')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Report::class)->end()
                                        ->scalarNode('repository')->defaultValue(ReportRepository::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('user_property')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(UserProperty::class)->end()
                                        ->scalarNode('repository')->defaultValue(UserPropertyRepository::class)->end()
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
