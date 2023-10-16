<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
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

    /**
     * @return iterable<int, ResourceInterface>
     */
    public function createPaginator(array $criteria = [], array $sorting = []): iterable
    {
        $queryBuilder = $this->getQueryBuilder();

        $this->applyCriteria($queryBuilder, $criteria);
        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }

    protected function getPaginator(QueryBuilder $queryBuilder): Pagerfanta
    {
        if (!class_exists(QueryAdapter::class)) {
            throw new \LogicException('You can not use the "paginator" if Pargefanta Doctrine ORM Adapter is not available. Try running "composer require pagerfanta/doctrine-orm-adapter".');
        }

        // Use output walkers option in the query adapter should be false as it affects performance greatly (see sylius/sylius#3775)
        return new Pagerfanta(new QueryAdapter($queryBuilder, false, false));
    }

    protected function getArrayPaginator($objects, int $page, int $limit): Pagerfanta
    {
        return $this->createPaginatorFromAdapter(new ArrayAdapter($objects), $page, $limit);
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @param array $criteria
     */
    protected function applyCriteria(QueryBuilder $queryBuilder, array $criteria = null)
    {
        if (null === $criteria) {
            return;
        }

        foreach ($criteria as $property => $value) {
            if (null === $value) {
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->isNull($this->getPropertyName($property)));
            } elseif (!is_array($value)) {
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->eq($this->getPropertyName($property), ':' . $property))
                    ->setParameter($property, $value);
            } else {
                $queryBuilder->andWhere($queryBuilder->expr()->in($this->getPropertyName($property), $value));
            }
        }
    }

    /**
     *
     * @param array $criteria
     * @return ArrayCollection
     */
    protected function parseCriteria(array $criteria) {
        return new ArrayCollection($criteria);
    }

    /**
     * @param QueryBuilder $qb
     *
     * @param array $sorting
     */
    protected function applySorting(QueryBuilder $qb, array $sorting = null)
    {
        if (null === $sorting) {
            return;
        }

        foreach ($sorting as $property => $order) {
            if (!empty($order)) {
                $qb->orderBy($this->getPropertyName($property), $order);
            }
        }
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getPropertyName($name)
    {
        if (false === strpos($name, '.')) {
            return $this->getAlias().'.'.$name;
        }

        return $name;
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder()
    {
        return $this->createQueryBuilder($this->getAlias());
    }

    public function getAlias()
    {
        return "e";
    }
}
