<?php

declare(strict_types=1);

namespace Talav\CoreBundle\DataTable\Adapter;

use Doctrine\ORM\QueryBuilder;
use Talav\CoreBundle\DataTable\DTO\DataTableState;

interface DoctrineAdapterType
{
    public function getQueryBuilder(DataTableState $state, array $options): QueryBuilder;
}
