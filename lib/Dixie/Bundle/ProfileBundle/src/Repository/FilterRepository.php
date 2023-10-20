<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Repository;

use Talav\ProfileBundle\Entity\Filter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FilterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Filter::class);
    }
}
