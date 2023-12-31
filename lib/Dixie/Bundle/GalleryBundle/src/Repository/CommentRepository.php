<?php

declare(strict_types=1);

namespace Talav\GalleryBundle\Repository;

use Doctrine\Common\Collections\Criteria;
use Talav\GalleryBundle\Entity\Comment;
use Talav\GalleryBundle\Entity\GalleryImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class CommentRepository.
 *
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
//class CommentRepository extends ServiceEntityRepository
class CommentRepository extends ServiceEntityRepository
{
    public const PAGINATOR_ITEMS_PER_PAGE = 5;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Object manager
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('comment')
            ->orderBy('comment.id', 'DESC');
    }

    /**
     * Query all records by image.
     *
     * @param GalleryImage $image Image entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByImage(GalleryImage $image): QueryBuilder
    {
        return $this->queryAll()
            ->select('partial comment.{id, email, nick, text}')
            ->where('comment.image = :imageId')
            ->setParameter('imageId', $image->getId());
    }

    public function findAllByImageQueryBuilder(GalleryImage $event): QueryBuilder
    {
        return $this
            ->createQueryBuilder('c')
            ->where('c.image = :image')
            ->setParameters([':image' => $event])
            ->orderBy('c.createdAt', Criteria::DESC)
            ;
    }
}
