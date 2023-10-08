<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Repository;

use Talav\Component\Resource\Repository\ResourceRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Talav\ProfileBundle\Entity\LikeInterface;

class LikeRepository extends ResourceRepository
{
//    public function flush(): void
//    {
//        $this->_em->flush();
//    }
//
//    public function persist(LikeInterface $entity): void
//    {
//        $this->_em->persist($entity);
//    }
//
//    public function add(Like $entity, bool $flush = true): void
//    {
//        $this->_em->persist($entity);
//        if ($flush) {
//            $this->_em->flush();
//        }
//    }
//
//    public function remove(Like $entity, bool $flush = true): void
//    {
//        $this->_em->remove($entity);
//        if ($flush) {
//            $this->_em->flush();
//        }
//    }

    // /**
    //  * @return Like[] Returns an array of Like objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Like
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
