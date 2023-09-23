<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Repository;

use Talav\ProfileBundle\Entity\UserMetadata;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserMetadata>
 */
class UserMetadataRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, UserMetadata::class);
	}
}
