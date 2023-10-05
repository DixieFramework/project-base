<?php

declare(strict_types=1);

namespace Talav\Component\Resource\Repository;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository as BaseEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Talav\Component\Resource\Model\ResourceInterface;

class ResourceRepository extends BaseEntityRepository implements RepositoryInterface
{
    use RepositoryPaginatorTrait {
//        RepositoryPaginatorTrait::__construct as repositoryPaginatorTraitConstruct;
    }

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

	/**
	 * Trouve une entité par sa clef primaire et renvoie une exception en cas d'absence.
	 *
	 * @psmal-return E
	 *
	 * @throws EntityNotFoundException
	 */
	public function findOrFail(int|string $id): object
	{
		$entity = $this->find($id, null, null);
		if (null === $entity) {
			throw EntityNotFoundException::fromClassNameAndIdentifier($this->_entityName, [(string) $id]);
		}

		return $entity;
	}

	/**
	 * @psmal-return E[]
	 */
	public function findByCaseInsensitive(array $conditions): array
	{
		/** @var E[] $result */
		$result = $this->findByCaseInsensitiveQuery($conditions)->getResult();

		return $result;
	}

	/**
	 * @psmal-return E|null
	 *
	 * @throws NonUniqueResultException
	 */
	public function findOneByCaseInsensitive(array $conditions): ?object
	{
		/** @var E|null $result */
		$result = $this->findByCaseInsensitiveQuery($conditions)
			->setMaxResults(1)
			->getOneOrNullResult();

		return $result;
	}

	/**
	 * @psalm-param E $entity
	 */
	public function save(object $entity, bool $flush = true): void
	{
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	/**
	 * @psalm-param E $entity
	 */
	public function delete(object $entity, bool $flush = true): void
	{
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	protected function findByCaseInsensitiveQuery(array $conditions): Query
	{
		$conditionString = [];
		$parameters = [];
		foreach ($conditions as $k => $v) {
			$conditionString[] = "LOWER(o.{$k}) = :{$k}";
			$parameters[$k] = strtolower($v);
		}

		return $this->createQueryBuilder('o')
			->where(join(' AND ', $conditionString))
			->setParameters($parameters)
			->getQuery();
	}
}
