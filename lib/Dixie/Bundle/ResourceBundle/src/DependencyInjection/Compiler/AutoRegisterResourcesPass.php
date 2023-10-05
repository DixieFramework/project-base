<?php

declare(strict_types=1);

namespace Talav\ResourceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Talav\Component\Resource\Factory\Factory;
use Talav\Component\Resource\Manager\ResourceManager;
use Talav\Component\Resource\Metadata\Resource as ResourceMetadata;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Reflection\ClassReflection;
use Talav\Component\Resource\Repository\ResourceRepository;
use function Symfony\Component\String\u;

final class AutoRegisterResourcesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        try {
            $resources = $container->getParameter('talav.resources');
            $registry = $container->findDefinition('talav.resource.registry');
        } catch (InvalidArgumentException $exception) {
            return;
        }

        $new = $this->autoRegisterResources($resources, $container);
        $container->setParameter('talav.resources', $new);

//        foreach ($resources as $alias => $configuration) {
//            $this->validateResource($configuration['classes']['model']);
//            $registry->addMethodCall('addFromAliasAndConfiguration', [$alias, $configuration]);
//        }
    }

    private function validateResource(string $class): void
    {
        if (!in_array(ResourceInterface::class, class_implements($class), true)) {
            throw new InvalidArgumentException(sprintf('Class "%s" must implement "%s" to be registered as a valid resource.', $class, ResourceInterface::class));
        }
    }

    private function autoRegisterResources(array &$resources, ContainerBuilder $container): array
    {
        /** @var array $resources */
//        $resources = $config['resources'];

        /** @var array $mapping */
        $mapping = $container->getParameter('talav.resource.mapping');
        $paths = $mapping['paths'] ?? [];

        /** @var class-string $className */
        foreach (ClassReflection::getResourcesByPaths($paths) as $className) {
            $resourceAttributes = ClassReflection::getClassAttributes($className, ResourceMetadata::class);

            foreach ($resourceAttributes as $resourceAttribute) {
                /** @var ResourceMetadata $resource */
                $resource = $resourceAttribute->newInstance();
                $resourceAlias = $this->getResourceAlias($resource, $className);

                if ($resources[$resourceAlias] ?? false) {
                    continue;
                }

                $resources[$resourceAlias] = [
                    'classes' => [
                        'model' => $className,
                        'factory' => Factory::class,
                        'manager' => ResourceManager::class,
                        'repository' => ResourceRepository::class
                    ],
                ];
            }
        }

        return $resources;
    }

    /** @param class-string $className */
    private function getResourceAlias(object $resource, string $className): string
    {
        $alias = $resource->getAlias();

        if (null !== $alias) {
            return $alias;
        }

        $reflectionClass = new \ReflectionClass($className);

        $shortName = $reflectionClass->getShortName();
        $suffix = 'Resource';
        if (str_ends_with($shortName, $suffix)) {
            $shortName = substr($shortName, 0, strlen($shortName) - strlen($suffix));
        }

        return 'app.' . u($shortName)->snake()->toString();
    }
}
