<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Talav\ProfileBundle\Entity\Attribute;

/**
 * @method Attribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attribute[]    findAll()
 * @method Attribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attribute::class);
    }

    public function findByCategory($categoryId): array
    {
        $query = $this->getEntityManager()->createQuery('SELECT a FROM DatingLibre\AppBundle\Entity\Attribute a
        WHERE a.category = :categoryId');

        $query->useQueryCache(true);
        $query->enableResultCache();
        $query->setParameter('categoryId', $categoryId);

        return $query->getResult();
    }

    public function findByCategoryName(string $categoryName): array
    {
        $query = $this->getEntityManager()->createQuery('SELECT a FROM DatingLibre\AppBundle\Entity\Attribute a
        INNER JOIN a.category c WHERE c.name = :categoryName');

        $query->useQueryCache(true);
        $query->enableResultCache();
        $query->setParameter('categoryName', $categoryName);

        return $query->getResult();
    }

    /**
     * @throws Exception
     */
    public function findByNames(array $attributeNames): array
    {
        $attributes = [];

        foreach ($attributeNames as $attributeName) {
            $attribute = $this->findOneBy(
                [
                    Attribute::NAME => $attributeName
                ]
            );

            if ($attribute === null) {
                throw new Exception(sprintf('Unrecognized attribute name: %s', $attributeName));
            }

            $attributes[] = $attribute;
        }

        return $attributes;
    }
}
