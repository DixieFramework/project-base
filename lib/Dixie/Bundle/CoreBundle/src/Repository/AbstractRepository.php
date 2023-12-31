<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Repository;

use Talav\Component\Resource\Repository\ResourceRepository;
use Talav\CoreBundle\Attribute\SortableEntity;
use Talav\CoreBundle\Entity\AbstractEntity;
use Talav\CoreBundle\Utils\StringUtils;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Base repository.
 *
 * @template T of AbstractEntity
 *
 * @template-extends ServiceEntityRepository<T>
 */
abstract class AbstractRepository extends ResourceRepository //extends ServiceEntityRepository
{
    /**
     * The default entity alias used to create query builder (value = 'e') .
     */
    final public const DEFAULT_ALIAS = 'e';

    /**
     * Add the given entity to the database.
     *
     * @param AbstractEntity $entity the entity to make managed and persistent
     * @param bool           $flush  true to flush change to the database
     *
     * @see AbstractRepository::flush()
     */
    public function add(AbstractEntity $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->flush();
        }
    }

    /**
     * Creates a default query builder.
     *
     * @param string $alias the entity alias
     *
     * @psalm-param literal-string $alias
     */
    public function createDefaultQueryBuilder(string $alias = self::DEFAULT_ALIAS): QueryBuilder
    {
        return $this->createQueryBuilder($alias);
    }

    /**
     * Flushes all changes to objects that have been queued to the database.
     *
     * This effectively synchronizes the in-memory state of managed objects with the
     * database.
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * Gets the default order of this entity.
     *
     * @return array<string, string> an array with the field as key and the order as value. An
     *                               empty array is returned if not attribute is found.
     *
     * @throws \ReflectionException if the class does not exist or if the validate parameter
     *                              is true and a property name is not found
     */
    public function getDefaultOrder(): array
    {
        return SortableEntity::getOrder($this->getEntityName());
    }

    /**
     * Gets sorted, distinct and not null values for the given column.
     *
     * @param string $field the field name (column) to get values for
     * @param string $value a value to search within the column or an empty string for all
     * @param int    $limit the maximum number of results to retrieve (the 'limit') or <code>-1</code> for all
     *
     * @return array an array, maybe empty; of matching values
     */
    public function getDistinctValues(string $field, string $value = '', int $limit = -1): array
    {
        $name = self::DEFAULT_ALIAS . '.' . $field;
        $builder = $this->createQueryBuilder(self::DEFAULT_ALIAS)
            ->select($name)
            ->distinct()
            ->orderBy($name);
        $expr = $builder->expr();
        if (StringUtils::isString($value)) {
            $param = 'search';
            $like = $expr->like($name, ':' . $param);
            $builder->where($like)
                ->setParameter($param, "%$value%");
        } else {
            /** @psalm-var literal-string $where */
            $where = $expr->isNotNull($name);
            $builder->where($where);
        }
        if ($limit > 0) {
            $builder->setMaxResults($limit);
        }

        return $builder->getQuery()
            ->getSingleColumnResult();
    }

    /**
     * Gets the database search fields.
     *
     * The default implementation returns the alias and the field separated by a dot ('.') character.
     *
     * @param string $field the field name
     * @param string $alias the entity alias
     *
     * @return string|string[] one on more database search fields
     */
    public function getSearchFields(string $field, string $alias = self::DEFAULT_ALIAS): array|string
    {
        return "$alias.$field";
    }

    /**
     * Creates a search query.
     *
     * @param array<string, string>  $sortedFields the sorted fields where key is the field name and value is the sort mode ('ASC' or 'DESC')
     * @param array<Criteria|string> $criteria     the filter criteria (the where clause)
     * @param string                 $alias        the entity alias
     *
     * @throws Query\QueryException
     * @throws \Doctrine\ORM\Exception\ORMException
     *
     * @see AbstractRepository::createDefaultQueryBuilder()
     *
     * @psalm-param literal-string $alias
     *
     * @psalm-return Query<T>
     */
    public function getSearchQuery(array $sortedFields = [], array $criteria = [], string $alias = self::DEFAULT_ALIAS): Query
    {
        $builder = $this->createDefaultQueryBuilder($alias);
        if ([] !== $criteria) {
            foreach ($criteria as $criterion) {
                if ($criterion instanceof Criteria) {
                    $builder->addCriteria($criterion);
                } else {
                    $builder->andWhere($criterion);
                }
            }
        }
        if ([] !== $sortedFields) {
            foreach ($sortedFields as $name => $order) {
                $field = $this->getSortField($name, $alias);
                $builder->addOrderBy($field, $order);
            }
        }

        return $builder->getQuery();
    }

    /**
     * Gets the name of the single identifier field. Note that this only works on
     * entity classes that have a single-field primary key.
     *
     * @throws \Doctrine\ORM\Exception\ORMException if the class doesn't have an identifier, or it has a composite primary key
     */
    public function getSingleIdentifierFieldName(): string
    {
        return $this->_class->getSingleIdentifierFieldName();
    }

    /**
     * Gets the database sort field.
     *
     * The default implementation returns the alias and the field separated by a dot ('.') character.
     *
     * @param string $field the field name
     * @param string $alias the entity alias
     *
     * @return string the sort field
     */
    public function getSortField(string $field, string $alias = self::DEFAULT_ALIAS): string
    {
        return "$alias.$field";
    }

    /**
     * Remove the given entity from the database.
     */
    public function remove(AbstractEntity $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->flush();
        }
    }

    /**
     * Add alias to the given fields.
     *
     * @param string   $alias the entity alias
     * @param string[] $names the fields to add alias
     *
     * @return string[] the fields with alias
     */
    protected function addPrefixes(string $alias, array $names): array
    {
        return \array_map(fn (string $name): string => "$alias.$name", $names);
    }

    /**
     * Concat fields.
     *
     * @param string   $alias   the entity prefix
     * @param string[] $fields  the fields to concat
     * @param string   $default the default value to use when a field is null
     *
     * @return string the concatenated fields
     */
    protected function concat(string $alias, array $fields, string $default = ''): string
    {
        $values = \array_map(fn (string $field): string => "COALESCE($alias.$field, '$default')", $fields);

        return \sprintf('CONCAT(%s)', \implode(',', $values));
    }

    /**
     * Gets the count distinct clause.
     *
     * @param string $alias the table alias
     * @param string $field the target field name
     */
    protected function getCountDistinct(string $alias, string $field): string
    {
        return "COUNT(DISTINCT $alias.id) AS $field";
    }
}
