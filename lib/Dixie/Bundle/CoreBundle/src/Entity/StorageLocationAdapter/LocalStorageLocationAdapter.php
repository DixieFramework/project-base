<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Entity\StorageLocationAdapter;

use Talav\CoreBundle\Container\EnvironmentAwareTrait;
use Talav\CoreBundle\Entity\Enums\StorageLocationAdapters;
use Talav\CoreBundle\Flysystem\Adapter\LocalAdapterInterface;
use Talav\CoreBundle\Flysystem\Adapter\LocalFilesystemAdapter;
use Talav\CoreBundle\Flysystem\ExtendedFilesystemInterface;
use Talav\CoreBundle\Flysystem\LocalFilesystem;
use InvalidArgumentException;
use Symfony\Component\Filesystem\Path;

final class LocalStorageLocationAdapter extends AbstractStorageLocationLocationAdapter
{
//    use EnvironmentAwareTrait;

    public function getType(): StorageLocationAdapters
    {
        return StorageLocationAdapters::Local;
    }

    public function getStorageAdapter(): LocalAdapterInterface
    {
        $filteredPath = self::filterPath($this->storageLocation->getPath());

        return new LocalFilesystemAdapter($filteredPath);
    }

    public function getFilesystem(): ExtendedFilesystemInterface
    {
        return new LocalFilesystem($this->getStorageAdapter());
    }

    public function validate(): void
    {
        // Check that there is any overlap between the specified path and the docroot.
        $path = $this->storageLocation->getPath();
        $baseDir = $this->environment->getBaseDirectory();

        if (Path::isBasePath($baseDir, $path)) {
            throw new InvalidArgumentException('Directory is within the web root.');
        }

        if (Path::isBasePath($path, $baseDir)) {
            throw new InvalidArgumentException('Directory is a parent directory of the web root.');
        }

        parent::validate();
    }
}
