<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Repository\Traits;

trait CountBy
{
    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countBy(array $criteria = []): int
    {
        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $this->createQueryBuilder('e');
        $qb->select('count(e.id)');

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

        return $qb->getQuery()->getSingleScalarResult();
    }
}
