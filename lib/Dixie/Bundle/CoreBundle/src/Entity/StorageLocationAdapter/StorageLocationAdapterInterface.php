<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Entity\StorageLocationAdapter;

use Talav\CoreBundle\Entity\Enums\StorageLocationAdapters;
use Talav\CoreBundle\Entity\StorageLocation;
use Talav\CoreBundle\Flysystem\Adapter\ExtendedAdapterInterface;
use Talav\CoreBundle\Flysystem\ExtendedFilesystemInterface;

interface StorageLocationAdapterInterface
{
    public function withStorageLocation(StorageLocation $storageLocation): static;

    public function getType(): StorageLocationAdapters;

    public function getStorageAdapter(): ExtendedAdapterInterface;

    public function getFilesystem(): ExtendedFilesystemInterface;

    public function validate(): void;

    public static function filterPath(string $path): string;

    public static function getUri(StorageLocation $storageLocation, ?string $suffix = null): string;
}
