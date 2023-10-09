<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Repository;

use Talav\CoreBundle\Doctrine\Repository;
use Talav\CoreBundle\Entity\Enums\StorageLocationAdapters;
use Talav\CoreBundle\Entity\Enums\StorageLocationTypes;
use Talav\CoreBundle\Entity\Station;
use Talav\CoreBundle\Entity\StorageLocation;
use Talav\CoreBundle\Entity\StorageLocationAdapter\StorageLocationAdapterInterface;
use InvalidArgumentException;
use Talav\CoreBundle\Traits\ContainerAwareTrait;

/**
 * @extends AbstractRepository<StorageLocation>
 */
final class StorageLocationRepository extends AbstractRepository
{
    use ContainerAwareTrait;

    protected string $entityClass = StorageLocation::class;

    public function findByType(
        string|StorageLocationTypes $type,
        int $id
    ): ?StorageLocation {
        if ($type instanceof StorageLocationTypes) {
            $type = $type->value;
        }

        return $this->findOneBy(
            [
                'type' => $type,
                'id' => $id,
            ]
        );
    }

    /**
     * @param string|StorageLocationTypes $type
     *
     * @return StorageLocation[]
     */
    public function findAllByType(string|StorageLocationTypes $type): array
    {
        if ($type instanceof StorageLocationTypes) {
            $type = $type->value;
        }

        return $this->findBy(
            [
                'type' => $type,
            ]
        );
    }

    /**
     * @param string|StorageLocationTypes $type
     * @param bool $addBlank
     * @param string|null $emptyString
     *
     * @return string[]
     */
    public function fetchSelectByType(
        string|StorageLocationTypes $type,
        bool $addBlank = false,
        ?string $emptyString = null
    ): array {
        $select = [];

        if ($addBlank) {
            $emptyString ??= __('None');
            $select[''] = $emptyString;
        }

        foreach ($this->findAllByType($type) as $storageLocation) {
            $select[$storageLocation->getId()] = (string)$storageLocation;
        }

        return $select;
    }

    public function createDefaultStorageLocations(): void
    {
        $backupLocations = $this->findAllByType(StorageLocationTypes::Backup);

        if (0 === count($backupLocations)) {
            $record = new StorageLocation(
                StorageLocationTypes::Backup,
                StorageLocationAdapters::Local
            );
            $record->setPath(StorageLocation::DEFAULT_BACKUPS_PATH);
            $this->em->persist($record);
        }

        $this->em->flush();
    }

    /**
     * @param StorageLocation $storageLocation
     *
     * @return Station[]
     */
    public function getStationsUsingLocation(StorageLocation $storageLocation): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('s')
            ->from(Station::class, 's');

        switch ($storageLocation->getType()) {
            case StorageLocationTypes::StationMedia:
                $qb->where('s.media_storage_location = :storageLocation')
                    ->setParameter('storageLocation', $storageLocation);
                break;

            case StorageLocationTypes::StationRecordings:
                $qb->where('s.recordings_storage_location = :storageLocation')
                    ->setParameter('storageLocation', $storageLocation);
                break;

            case StorageLocationTypes::StationPodcasts:
                $qb->where('s.podcasts_storage_location = :storageLocation')
                    ->setParameter('storageLocation', $storageLocation);
                break;

            case StorageLocationTypes::Backup:
                return [];
        }

        return $qb->getQuery()->execute();
    }

    public function getAdapter(StorageLocation $storageLocation): StorageLocationAdapterInterface
    {
        $adapterClass = $storageLocation->getAdapter()->getAdapterClass();

        if (!$this->di->has($adapterClass)) {
            throw new InvalidArgumentException(sprintf('Class not found: %s', $adapterClass));
        }

        /** @var StorageLocationAdapterInterface $adapter */
        $adapter = $this->di->get($adapterClass);
        return $adapter->withStorageLocation($storageLocation);
    }
}
