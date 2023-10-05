<?php

declare(strict_types=1);

namespace Talav\ResourceBundle\DependencyInjection;

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
use Talav\ProfileBundle\Entity\UserRelation;
use Talav\ProfileBundle\Repository\FriendshipRepository;
use Talav\ProfileBundle\Repository\FriendshipRequestRepository;
use Talav\ProfileBundle\Repository\LikeRepository;
use Talav\ProfileBundle\Repository\MessageRepository;
use Talav\ProfileBundle\Repository\NotificationRepository;
use Talav\ProfileBundle\Repository\ReportRepository;
use Talav\ProfileBundle\Repository\UserFriendRepository;
use Talav\ProfileBundle\Repository\UserRelationRepository;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    final public const ROOT_NODE = 'talav_resource';

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

        $rootNode
            ->children()
                ->arrayNode('mapping')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('paths')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
