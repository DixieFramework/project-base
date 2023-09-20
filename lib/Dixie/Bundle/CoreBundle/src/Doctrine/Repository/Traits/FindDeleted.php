<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Repository\Traits;

use Doctrine\ORM\Query;

trait FindDeleted
{
    public function findDeleted(array $orderBy = [], $limit = null, $offset = null): Query
    {
        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.deleted_at IS NOT NULL');

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

        return $qb->getQuery()->getResult();
    }
}
