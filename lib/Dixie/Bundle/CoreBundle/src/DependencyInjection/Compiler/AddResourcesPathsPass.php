<?php

declare(strict_types=1);

namespace Talav\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Path;

/**
 * @internal
 */
class AddResourcesPathsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container->setParameter('talav.resources_paths', $this->getResourcesPaths($container));
    }

    /**
     * @return array<string>
     */
    private function getResourcesPaths(ContainerBuilder $container): array
    {
        $paths = [];
        $projectDir = $container->getParameter('kernel.project_dir');

        $bundles = $container->getParameter('kernel.bundles');
        $meta = $container->getParameter('kernel.bundles_metadata');

        foreach ($bundles as $name => $class) {
//            if (AbstractRiaBundle::class === $class) {
//                $paths[] = $meta[$name]['path'];
//            } else
            if (is_dir($path = Path::join($meta[$name]['path'], 'Resources/ria'))) {
                $paths[] = $path;
            } elseif (is_dir($path = Path::join($meta[$name]['path'], 'resources'))) {
                $paths[] = $path;
            }
        }

        if (is_dir($path = Path::join($projectDir, 'resources'))) {
            $paths[] = $path;
        }

        return $paths;
    }
}
