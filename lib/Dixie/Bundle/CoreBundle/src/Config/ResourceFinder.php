<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Config;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;

/**
 * Creates a Finder object with the bundle paths set.
 */
//#[Autoconfigure(bind: ['$paths' => '%talav.resources_paths%'], constructor: 'create')]
class ResourceFinder implements ResourceFinderInterface
{
    private array $paths;

    public static function create($paths): self
    {
        $self = new self();
        $self->paths = $paths;
        // ...

        return $self;
    }

//    /**
//     * @param string|array $paths
//     */
//    public function __construct(#[Autowire('%talav.resources_paths%')] string|array $paths)
//    {
//        $this->paths = (array) $paths;
//    }

    public function find(): Finder
    {
        return Finder::create()->in($this->paths);
    }

    public function findIn(string $subpath): Finder
    {
        return Finder::create()->in($this->getExistingSubpaths($subpath));
    }

    /**
     * @return array<string>
     */
    private function getExistingSubpaths(string $subpath): array
    {
        $paths = [];

        foreach ($this->paths as $path) {
            if (is_dir($dir = Path::join($path, $subpath))) {
                $paths[] = $dir;
            }
        }

        if (empty($paths)) {
            throw new \InvalidArgumentException(sprintf('The subpath "%s" does not exists.', $subpath));
        }

        return $paths;
    }
}
