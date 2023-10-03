<?php

declare(strict_types=1);

namespace Talav\PostBundle\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Talav\Component\Resource\Repository\ResourceRepository;
use Talav\PostBundle\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Talav\PostBundle\Entity\PostInterface;

class CommentRepository extends ResourceRepository
{
    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Comment $entity): void
    {
        $this->_em->persist($entity);
    }

    public function add(Comment $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Comment $entity, bool $flush = true): void
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

    public function findAllByEventQueryBuilder(PostInterface $post): QueryBuilder
    {
        return $this
            ->createQueryBuilder('c')
            ->where('c.post = :post AND c.parent IS NULL AND c.status = 1')
            ->setParameters([':post' => $post])
            ->orderBy('c.publishedAt', Criteria::DESC)
            ;
    }

    /**
     * @return Comment[]
     */
    public function findAllByUser(User $user): array
    {
        return $this
            ->createQueryBuilder('c')
            ->where('c.user = :user')
            ->setParameters([':user' => $user->getId()])
            ->getQuery()
            ->execute();
    }

    public function findAllAnswersQueryBuilder(Comment $comment): QueryBuilder
    {
        return $this
            ->createQueryBuilder('c')
            ->where('c.parent = :parent AND c.approved = true')
            ->setParameters([':parent' => $comment])
            ->orderBy('c.publishedAt', Criteria::DESC);
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
