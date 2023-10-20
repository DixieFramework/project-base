<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Repository;

use Talav\ProfileBundle\Entity\Attribute;
use Talav\ProfileBundle\Entity\AttributeCategory;
use Talav\ProfileBundle\Entity\ProfileInterface;
use Talav\ProfileBundle\Entity\UserAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAttribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAttribute[]    findAll()
 * @method UserAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAttribute::class);
    }

    public function deleteByCategory(ProfileInterface $profile, AttributeCategory $categoryId): void
    {
        $query = $this->getEntityManager()
            ->createNativeQuery(<<<EOD
DELETE FROM user_attribute ua
USING attribute a
WHERE a.id = ua.attribute_id
AND ua.profile_id = :profileId
AND a.attributeCategory = :categoryId
EOD, new ResultSetMapping());

        $query->setParameter(':profileId', $profile->getId());
        $query->setParameter('categoryId', $categoryId->getId());
        $query->execute();
    }

    private function getByUserAndCategoryQuery(ProfileInterface $profile, string $categoryName): NativeQuery
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Attribute::class, 'a');
        $rsm->addFieldResult('a', 'id', 'id');
        $rsm->addFieldResult('a', 'name', 'name');

        $query = $this->getEntityManager()
            ->createNativeQuery(<<<EOD
SELECT a.id, a.name FROM user_attribute ua
INNER JOIN attribute a ON a.id = ua.attribute_id
INNER JOIN attribute_category c ON a.attribute_category_id = c.id
WHERE ua.profile_id = :profileId
AND LOWER(c.name) = LOWER(:categoryName)
EOD, $rsm);

        $query->setParameter(':profileId', $profile->getId());
        $query->setParameter('categoryName', $categoryName);

        return $query;
    }


    public function findOneByUserAndCategory(ProfileInterface $profile, string $categoryName): ?Attribute
    {
        return $this->getByUserAndCategoryQuery($profile, $categoryName)
            ->getOneOrNullResult();
    }

    public function findMultipleByUserAndCategory(ProfileInterface $profile, string $categoryName): array
    {
        return $this->getByUserAndCategoryQuery($profile, $categoryName)
            ->getResult();
    }

    public function findAttributesByUser(ProfileInterface $profile): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Attribute::class, 'a');
        $rsm->addFieldResult('a', 'id', 'id');
        $rsm->addFieldResult('a', 'name', 'name');

        $query = $this->getEntityManager()
            ->createNativeQuery(<<<EOD
SELECT a.id, a.name FROM user_attribute ua
INNER JOIN attribute a ON a.id = ua.attribute_id
WHERE ua.profile_id = :profileId
EOD, $rsm);

        $query->setParameter(':profileId', $profile->getId());

        return $query->getResult();
    }

    public function deleteByUser(ProfileInterface $profile): void
    {
        $query = $this->getEntityManager()
            ->createNativeQuery(<<<EOD
DELETE FROM user_attribute ua
WHERE ua.profile_id = :profileId
EOD, new ResultSetMapping());

        $query->setParameter(':profileId', $profile->getId());
        $query->execute();
    }
}
