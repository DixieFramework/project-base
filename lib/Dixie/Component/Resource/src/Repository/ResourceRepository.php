<?php

declare(strict_types=1);

namespace Talav\Component\Resource\Repository;

use Doctrine\ORM\EntityRepository as BaseEntityRepository;
use Talav\Component\Resource\Model\ResourceInterface;

class ResourceRepository extends BaseEntityRepository implements RepositoryInterface
{
    use RepositoryPaginatorTrait;

	public const ORDER_TYPES = [
		'ASC', 'DESC'
	];

	protected function getOrderType(string $type, ?string $default = null): ?string
	{
		return false === in_array($type, self::ORDER_TYPES, true) ? $default : $type;
	}

	/**
	 * @return array<ResourceInterface>
	 */
	protected function findByCriteria(array $criteria, array $orderBy = []): array
	{
		$qb = $this->getQueryBuilder();

		foreach ($orderBy as $propertyName => $as) {
			$qb->orderBy($propertyName, $as);
		}

		foreach ($criteria as $propertyName => $value) {
			$parameterName = str_replace('.', '_', $propertyName);

			$qb->andWhere("${propertyName} = :${parameterName}");
			$qb->setParameter($parameterName, $value);
		}

		return $qb->getQuery()->getResult() ?: [];
	}
}
