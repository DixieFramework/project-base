<?php

declare(strict_types=1);

namespace Talav\Component\Resource\Repository;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\FixedAdapter;
use Pagerfanta\Doctrine\Collections\CollectionAdapter;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Talav\WebBundle\Pagerfanta\FixedPaginate;

trait RepositoryPaginatorTrait
{
//    public function __construct(private ParameterBagInterface $parameterBag)
//    {
//    }

    /**
     * {@inheritdoc}
     */
    public function createPaginator(array $criteria = [], array $sorting = []): Pagerfanta
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $this->applyCriteria($queryBuilder, $criteria);
        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }

    protected function getPaginator(QueryBuilder $queryBuilder): Pagerfanta
    {
        // Use output walkers option in DoctrineORMAdapter should be false as it affects performance greatly
        return new Pagerfanta(new QueryAdapter($queryBuilder, false, false));
    }

    protected function applyCriteria(QueryBuilder $queryBuilder, array $criteria = []): void
    {
        foreach ($criteria as $property => $value) {
            if (!in_array(
                $property,
                array_merge($this->_class->getAssociationNames(), $this->_class->getFieldNames()),
                true
            )) {
                continue;
            }

            $name = $this->getPropertyName($property);

            if (null === $value) {
                $queryBuilder->andWhere($queryBuilder->expr()->isNull($name));
            } elseif (is_array($value)) {
                $queryBuilder->andWhere($queryBuilder->expr()->in($name, $value));
            } elseif ('' !== $value) {
                $parameter = str_replace('.', '_', $property);
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->eq($name, ':'.$parameter))
                    ->setParameter($parameter, $value)
                ;
            }
        }
    }

    protected function applySorting(QueryBuilder $queryBuilder, array $sorting = []): void
    {
        foreach ($sorting as $property => $order) {
            if (!in_array(
                $property,
                array_merge($this->_class->getAssociationNames(), $this->_class->getFieldNames()),
                true
            )) {
                continue;
            }

            if (!empty($order)) {
                $queryBuilder->addOrderBy($this->getPropertyName($property), $order);
            }
        }
    }

    protected function getPropertyName(string $name): string
    {
        if (false === strpos($name, '.')) {
            return 'o.'.$name;
        }

        return $name;
    }

    // !----

    public function paginate(Query|QueryBuilder|Collection|array|FixedPaginate $data, int $page = 1, int $maxPerPage = null): Pagerfanta
    {
        if ($data instanceof Collection) {
            $adapter = new CollectionAdapter($data);
        } elseif ($data instanceof Query || $data instanceof QueryBuilder) {
            $adapter = new QueryAdapter($data, false);
        } elseif (\is_array($data)) {
            $adapter = new ArrayAdapter($data);
        } elseif ($data instanceof FixedPaginate) {
            $adapter = new FixedAdapter($data->getNbResults(), $data->getResults());
        } else {
            throw new \RuntimeException('Неизвестный тип данных для постраничной навигации');
        }

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($maxPerPage ?? $this->parameterBag->get('talav_paginate_maxperpage'));
        $pagerfanta->setCurrentPage(
            $this->normalizePage($pagerfanta, $page)
        );

        return $pagerfanta;
    }

    protected function normalizePage(Pagerfanta $pagerfanta, int $page): int
    {
        $maxPage = $pagerfanta->getNbPages();
        $minPage = 1;
        $currentPage = \max($page, $minPage);

        return \min($currentPage, $maxPage);
    }
}
