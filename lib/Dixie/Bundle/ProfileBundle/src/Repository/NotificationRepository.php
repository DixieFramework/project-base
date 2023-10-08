<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Repository;

use Talav\Component\Resource\Repository\ResourceRepository;
use Talav\ProfileBundle\Entity\Notification;
use Talav\PostBundle\Entity\PostInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NotificationRepository extends ResourceRepository
{
    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Notification $entity): void
    {
        $this->_em->persist($entity);
    }

    public function add(Notification $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Notification $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findPostNotifications(PostInterface $post)
    {
        return $this->createQueryBuilder('n')
            ->where('n.type = \'post_approved\'')
            ->orWhere('n.type = \'post_rejected\'')
            ->orWhere('n.type = \'user_tagged\'')
            ->andWhere('n.post = :post')
            ->setParameter('post', $post)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Notification[] Returns an array of Notification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Notification
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
