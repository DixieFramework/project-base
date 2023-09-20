<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\PagerfantaInterface;

trait PaginateTrait
{
    protected function createQueryBuilderPaginator(QueryBuilder $queryBuilder, int $page, int $limit): PagerfantaInterface
    {
        $adapter = new QueryAdapter($queryBuilder);

        return $this->createPaginatorFromAdapter($adapter, $page, $limit);
    }

    protected function createEmptyPaginator(int $page, int $limit): PagerfantaInterface
    {
        $adapter = new ArrayAdapter([]);

        return $this->createPaginatorFromAdapter($adapter, $page, $limit);
    }

    protected function createPaginatorFromAdapter(AdapterInterface $adapter, int $page, int $limit): PagerfantaInterface
    {
        $pagerfanta = new Pagerfanta($adapter);

        $this->updatePaginator($pagerfanta, $page, $limit);

        return $pagerfanta;
    }

    protected function updatePaginator(PagerfantaInterface $pagerfanta, int $page, int $limit): void
    {
        $pagerfanta
            ->setAllowOutOfRangePages(true)
            ->setMaxPerPage($limit)
            ->setCurrentPage($page);
    }
}
