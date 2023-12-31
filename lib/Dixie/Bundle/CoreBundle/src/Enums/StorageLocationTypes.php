<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Enums;

enum StorageLocationTypes: string
{
    case Backup = 'backup';
    case StationMedia = 'station_media';
    case StationRecordings = 'station_recordings';
    case StationPodcasts = 'station_podcasts';
}
