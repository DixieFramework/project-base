<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Enums;

use Talav\CoreBundle\Entity\StorageLocationAdapter\DropboxStorageLocationAdapter;
use Talav\CoreBundle\Entity\StorageLocationAdapter\LocalStorageLocationAdapter;
use Talav\CoreBundle\Entity\StorageLocationAdapter\S3StorageLocationAdapter;
use Talav\CoreBundle\Entity\StorageLocationAdapter\SftpStorageLocationAdapter;
use Talav\CoreBundle\Entity\StorageLocationAdapter\StorageLocationAdapterInterface;

enum StorageLocationAdapters: string
{
    case Local = 'local';
    case S3 = 's3';
    case Dropbox = 'dropbox';
    case Sftp = 'sftp';

    public function isLocal(): bool
    {
        return self::Local === $this;
    }

    public function getName(): string
    {
        return match ($this) {
            self::Local => 'Local',
            self::S3 => 'S3',
            self::Dropbox => 'Dropbox',
            self::Sftp => 'SFTP',
        };
    }

    /**
     * @return class-string<StorageLocationAdapterInterface>
     */
    public function getAdapterClass(): string
    {
        return match ($this) {
            self::Local => LocalStorageLocationAdapter::class,
            self::S3 => S3StorageLocationAdapter::class,
            self::Dropbox => DropboxStorageLocationAdapter::class,
            self::Sftp => SftpStorageLocationAdapter::class
        };
    }
}
