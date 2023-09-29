<?php

declare(strict_types=1);

namespace Talav\BundleGeneratorBundle\Model;

use Symfony\Component\DependencyInjection\Container;

/**
 * Represents a bundle being built.
 *
 * The following class is copied from \Sensio\Bundle\BundleGeneratorBundle\Model\Bundle
 */
class BaseBundle
{
    private $namespace;

    private $name;

    private $targetDirectory;

    private $configurationFormat;

    private $isShared;

    private string $license;

    private $testsDirectory;


    public function __construct($namespace, $name, $targetDirectory, $configurationFormat, $isShared, string $license = 'MIT')
    {
        $this->namespace = $namespace;
        $this->name = $name;
        $this->targetDirectory = $targetDirectory;
        $this->configurationFormat = $configurationFormat;
        $this->isShared = $isShared;
        $this->license = $license;
        $this->testsDirectory = $this->getTargetDirectory().'/tests';
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getConfigurationFormat()
    {
        return $this->configurationFormat;
    }

    public function isShared()
    {
        return $this->isShared;
    }

    /**
     * Returns the directory where the bundle will be generated.
     *
     * @return string
     */
    public function getTargetDirectory()
    {
        return rtrim($this->targetDirectory, '/').'/'.trim(strtr($this->namespace, '\\', '/'), '/');
    }

    public function getRelativeTargetDirectory()
    {
        return ltrim(str_replace(DIXIE_PROJECT_ROOT, '', $this->getTargetDirectory()), '/');
    }

    /**
     * Returns the name of the bundle without the Bundle suffix.
     *
     * @return string
     */
    public function getBasename()
    {
        return substr($this->name, 0, -6);
    }

    /**
     * Returns the dependency injection extension alias for this bundle.
     *
     * @return string
     */
    public function getExtensionAlias()
    {
        return Container::underscore($this->getBasename());
    }

    /**
     * Should a DependencyInjection directory be generated for this bundle?
     *
     * @return bool
     */
    public function shouldGenerateDependencyInjectionDirectory()
    {
        return $this->isShared;
    }

    /**
     * What is the filename for the services.yaml/xml file?
     *
     * @return string
     */
    public function getServicesConfigurationFilename()
    {
        if ('yaml' === $this->getConfigurationFormat() || 'annotation' === $this->configurationFormat) {
            return 'services.yaml';
        } else {
            return 'services.'.$this->getConfigurationFormat();
        }
    }

    /**
     * What is the filename for the routing.yaml/xml file?
     *
     * If false, no routing file will be generated
     *
     * @return string|bool
     */
    public function getRoutingConfigurationFilename()
    {
        if ($this->getConfigurationFormat() == 'annotation') {
            return false;
        }

        return 'routing.'.$this->getConfigurationFormat();
    }

    /**
     * Returns the class name of the Bundle class.
     *
     * @return string
     */
    public function getBundleClassName()
    {
        return $this->namespace.'\\'.$this->name;
    }

    /**
     * @return string
     */
    public function getLicense(): string
    {
        return $this->license;
    }

    public function setTestsDirectory($testsDirectory)
    {
        $this->testsDirectory = $testsDirectory;
    }

    public function getTestsDirectory()
    {
        return $this->testsDirectory;
    }

}
