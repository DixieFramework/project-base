<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Repository\Traits;

use Doctrine\ORM\Query;

trait FindByQuery
{
    public function getFindByQuery(array $criteria = [], array $orderBy = [], $limit = null, $offset = null): Query
    {
        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $this->createQueryBuilder('e');

        $firstCriteria = true;
        foreach ($criteria as $field => $value) {
            if ($firstCriteria) {
                if ($value === null or (is_string($value) and strtolower($value) === 'null')) {
                    $qb->where("e.$field IS NULL");
                } elseif (is_string($value) and strtolower($value) === 'not null') {
                    $qb->where("e.$field IS NOT NULL");
                } else {
                    $qb->where("e.$field = :$field");
                    $qb->setParameter($field, $value);
                }

                $firstCriteria = false;
            } else {
                if ($value === null or (is_string($value) and strtolower($value) === 'null')) {
                    $qb->andWhere("e.$field IS NULL");
                } elseif (is_string($value) and strtolower($value) === 'not null') {
                    $qb->andWhere("e.$field IS NOT NULL");
                } else {
                    $qb->andWhere("e.$field = :$field");
                    $qb->setParameter($field, $value);
                }
            }
        }

        $firstOrderBy = true;
        foreach ($orderBy as $field => $value) {
            if ($firstOrderBy) {
                $qb->orderBy("e.$field", $value);
                $firstOrderBy = false;
            } else {
                $qb->addOrderBy("e.$field", $value);
            }
        }

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        if ($offset !== null) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery();
    }
}
