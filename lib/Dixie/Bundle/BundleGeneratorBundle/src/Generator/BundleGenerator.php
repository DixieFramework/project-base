<?php

declare(strict_types=1);

namespace Talav\BundleGeneratorBundle\Generator;

use Groshy\Kernel;
use Symfony\Component\DependencyInjection\Container;
use Talav\BundleGeneratorBundle\Manipulator\RoutingManipulator;
use Talav\BundleGeneratorBundle\Model\Bundle;
use Talav\CoreBundle\Application\Constant\AppConstants;

class BundleGenerator extends BaseBundleGenerator
{
    public function generateBundle(Bundle $bundle)
    {
        parent::generateBundle($bundle);

        $dir = $bundle->getTargetDirectory();
//
//        $parameters = [
//            'namespace' => $bundle->getNamespace(),
//            'bundle' => $bundle->getName(),
//            'format' => $bundle->getConfigurationFormat(),
//            'bundle_basename' => $bundle->getBasename(),
//            'extension_alias' => $bundle->getExtensionAlias(),
//        ];

        $basename = substr($bundle->getName(), 0, -6);
        $namespaceParts = explode('\\', $bundle->getNamespace());
        $vendorName = $namespaceParts[0];
        $bundleName = substr($bundle->getName(), strlen($vendorName), strlen($bundle->getName()));
        $parameters = [
            'vendor'            => $vendorName,
            'namespace'         => $bundle->getNamespace(),
            'bundle'            => $bundle->getName(),
            'bundle_name'       => $bundleName,
            'bundle_short_name' => $this->normalizeBundleName($bundleName),
            'namespace_double'  => str_replace('\\', '\\\\', $bundle->getNamespace()),
            'bundle_double'     => str_replace('\\', '\\\\', $bundle->getName()),
            'format'            => $bundle->getConfigurationFormat(),
            'bundle_basename'   => $basename,
            'extension_alias'   => Container::underscore($basename),
            'bundle_underscore' => Container::underscore($bundle->getName()),
            'zikulaVersion'     => Kernel::VERSION,
            'php_min'           => Kernel::PHP_MINIMUM_VERSION
        ];

//        $routingFilename = $bundle->getRoutingConfigurationFilename() ?: 'routing.yml';
//        $routingTarget = $dir . '/config/talav/' . $routingFilename;
//
//        // create routing file for default annotation
//        if ($bundle->getConfigurationFormat() == 'annotation') {
//            self::mkdir(dirname($routingTarget));
//            self::dump($routingTarget, '');
//
//            $routing = new RoutingManipulator($routingTarget);
//            $routing->addResource($bundle->getName(), 'annotation');
//        } else {
//            // update routing file created by default implementation
//            $this->renderFile(
//                sprintf('bundle/%s.twig', $routingFilename),
//                $dir.'/config/talav/'.$routingFilename, $parameters
//            );
//        }

//        $this->renderFile(
//            'js/pimcore/startup.js.twig',
//            $dir . '/public/js/pimcore/startup.js',
//            $parameters
//        );
//        $this->renderFile('bundle/LICENSE-'.$bundle->getLicense().'.twig', $dir.'/LICENSE.md', $parameters);
        $this->renderFile('bundle/talav_routing.yml.twig', $dir.'/Resources/config/talav/routing.yml', $parameters);
        $this->renderFile('bundle/composer.json.twig', $dir.'/composer.json', $parameters);

        $this->filesystem->mkdir($dir.'/Resources/public');
        $this->filesystem->touch($dir.'/Resources/public/.gitkeep');
    }

    private function normalizeBundleName(string $name, bool $caseInsensitive = false): string
    {
        $name = $caseInsensitive ? strtolower($name) : $name;
        $suffix = $caseInsensitive ? 'bundle' : 'Bundle';

        if (str_ends_with($name, $suffix)) {
            $name = substr($name, 0, -6);
        }

        return $name;
    }
}
