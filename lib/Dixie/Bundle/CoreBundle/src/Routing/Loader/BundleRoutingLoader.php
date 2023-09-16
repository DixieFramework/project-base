<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Routing\Loader;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use Talav\CoreBundle\Config\BundleConfigLocator;

/**
 * @internal
 */
class BundleRoutingLoader extends Loader
{
    private BundleConfigLocator $locator;

    public function __construct(BundleConfigLocator $locator)
    {
        $this->locator = $locator;
    }

    public function load(mixed $resource, string $type = null): mixed
    {
        $collection = new RouteCollection();
        $files = $this->locator->locate('routing');

        if (empty($files)) {
            return $collection;
        }

        foreach ($files as $file) {
            $routes = $this->import($file);
            $collection->addCollection($routes);
        }

        return $collection;
    }

    public function supports(mixed $resource, string $type = null): bool
    {
        return 'talav_bundle' === $type;
    }
}
