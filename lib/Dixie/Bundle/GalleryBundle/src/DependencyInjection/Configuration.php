<?php

declare(strict_types=1);

namespace Talav\GalleryBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Talav\Component\Resource\Manager\ResourceManager;
use Talav\GalleryBundle\Entity\Gallery;
use Talav\GalleryBundle\Entity\Image;
use Talav\GalleryBundle\Repository\GalleryRepository;
use Talav\GalleryBundle\Repository\ImageRepository;

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
        $treeBuilder = new TreeBuilder('talav_gallery');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

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
                        ->arrayNode('gallery')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Gallery::class)->end()
                                        ->scalarNode('manager')->defaultValue(ResourceManager::class)->end()
                                        ->scalarNode('repository')->defaultValue(GalleryRepository::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('gallery_image')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Image::class)->end()
                                        ->scalarNode('manager')->defaultValue(ResourceManager::class)->end()
                                        ->scalarNode('repository')->defaultValue(ImageRepository::class)->end()
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
