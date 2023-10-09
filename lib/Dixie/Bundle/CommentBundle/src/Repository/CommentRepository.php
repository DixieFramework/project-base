<?php

declare(strict_types=1);

namespace Talav\CommentBundle\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Talav\CommentBundle\Entity\CommentInterface;
use Talav\Component\Resource\Repository\ResourceRepository;
use Talav\Component\User\Model\UserInterface;
use Talav\PostBundle\Entity\PostInterface;

class CommentRepository extends ResourceRepository
{
    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(CommentInterface $entity): void
    {
        $this->_em->persist($entity);
    }

    public function add(CommentInterface $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(CommentInterface $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getNoParentComments($criteria, $orderBy = ['id' => 'DESC'], $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('c');

        $qb->leftJoin('c.parent','p')
            ->where('p.id is null')
        ;

        foreach ($criteria as $property => $value) {
            if ($property == 'post') {
                $qb ->andWhere('c.post = :post')
                    ->setParameter('post', $criteria['post']);
            } elseif ($property == 'song') {
                $qb ->andWhere('c.song = :song')
                    ->setParameter('song', $criteria['song']);
            } else {
                $qb ->andWhere('s.'. $property .' = :' . $property . '')
                    ->setParameter($property,$value)
                ;
            }
        }

        foreach ($orderBy as $key => $value) {
            $qb->orderBy('c.'.$key,$value);
        }

        $qb ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $qb->getQuery()->getResult();
    }

    public function findAllQueryBuilder($criteria, $orderBy = ['id' => 'DESC'], $limit = null, $offset = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c');

        $qb->leftJoin('c.parent','p')
            ->where('p.id IS NULL')
        ;

        foreach ($criteria as $property => $value) {
            if ($property == 'post') {
                $qb ->andWhere('c.post = :post')
                    ->setParameter('post', $criteria['post']);
            } elseif ($property == 'song') {
                $qb ->andWhere('c.song = :song')
                    ->setParameter('song', $criteria['song']);
            } else {
                $qb ->andWhere('s.'. $property .' = :' . $property . '')
                    ->setParameter($property,$value)
                ;
            }
        }

        foreach ($orderBy as $key => $value) {
            $qb->orderBy('c.'.$key,$value);
        }

        return $qb;
    }

    public function findAllByPostQueryBuilder(PostInterface $post): QueryBuilder
    {
        return $this
            ->createQueryBuilder('c')
            ->where('c.post = :post AND c.parent IS NULL AND c.status = 1')
            ->setParameters([':post' => $post])
            ->orderBy('c.publishedAt', Criteria::DESC)
            ;
    }

    /**
     * @return CommentInterface[]
     */
    public function findAllByUser(UserInterface $user): array
    {
        return $this
            ->createQueryBuilder('c')
            ->where('c.user = :user')
            ->setParameters([':user' => $user->getId()])
            ->getQuery()
            ->execute();
    }

    public function findAllAnswersQueryBuilder(CommentInterface $comment): QueryBuilder
    {
        return $this
            ->createQueryBuilder('c')
            ->where('c.parent = :parent AND c.approved = true')
            ->setParameters([':parent' => $comment])
            ->orderBy('c.publishedAt', Criteria::DESC);
    }

    public function findCommentsByTypeAndEntityQueryBuilder(string $type, int $entityId): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.author', 'author')
            ->where('c.type = :type AND c.entityId = :entityId')
            ->andWhere('AND c.parent IS NULL AND c.status = 1')
            ->setParameter('type', $type)
            ->setParameter('entityId', $entityId)
            ->orderBy('c.publishedAt', Criteria::DESC)
        ;

        return $qb;

        if ($limit) {
            $query->setMaxResults($limit);
        }

        if ($offset) {
            $query->setFirstResult($offset);
        }

        /** @var CommentInterface[] $result */
        $result = $query->getResult();

        return $result;
    }

    public function findComments(string $type, string $entityId, int $limit = 10, int $offset = 0): array
    {
        $query = $this->createQueryBuilder('c')
            ->leftJoin('c.author', 'author')
            ->where('c.type = :type AND c.entityId = :entityId')
            ->setParameter('type', $type)
            ->setParameter('entityId', $entityId)
            ->orderBy('c.publishedAt', 'DESC')
            ->getQuery();

        if ($limit) {
            $query->setMaxResults($limit);
        }

        if ($offset) {
            $query->setFirstResult($offset);
        }

        /** @var CommentInterface[] $result */
        $result = $query->getResult();

        return $result;
    }

    /**
     * @param string $fieldName
     * @param int[] $entityIds
     *
     * @return QueryBuilder
     */
    public function getBaseQueryBuilder($fieldName, $entityIds)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->andWhere('c.' . $fieldName . ' in (:param1)');
        $qb->setParameter('param1', $entityIds);

        return $qb;
    }

    /**
     * @param string $fieldName
     * @param int[] $entityIds
     *
     * @return QueryBuilder
     */
    public function getNumberOfComment($fieldName, $entityIds)
    {
        $qb = $this->getBaseQueryBuilder($fieldName, $entityIds);
        $qb->select($qb->expr()->count('c.id'));

        return $qb;
    }

    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
