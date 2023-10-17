<?php

declare(strict_types=1);

namespace Talav\GalleryBundle\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Talav\Component\Resource\Repository\ResourceRepository;
use Talav\Component\User\Model\UserInterface;
use Talav\GalleryBundle\Entity\Gallery;
use Talav\GalleryBundle\Entity\GalleryImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class ImageRepository.
 *
 * @extends ServiceEntityRepository<GalleryImage>
 *
 * @method GalleryImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method GalleryImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method GalleryImage[]    findAll()
 * @method GalleryImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
//class ImageRepository extends ServiceEntityRepository
class GalleryImageRepository extends ResourceRepository
{
    public const PAGINATOR_ITEMS_PER_PAGE = 5;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Object manager
     */
//    public function __construct(ManagerRegistry $registry)
//    {
//        parent::__construct($registry, Image::class);
//    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('image')
            ->orderBy('image.id', 'DESC');
    }

//    public function findAllByGalleryQueryBuilder(Gallery $gallery): QueryBuilder
//    {
//        return $this
//            ->createQueryBuilder('i')
//            ->leftJoin('i.user', 'u')
//            ->where('i.gallery = :gallery')
//            ->andWhere('g.user = :user')
//            ->setParameters(['user' => $gallery, 'gallery' => $gallery])
//            ->orderBy('g.id', Criteria::DESC);
//    }

    public function findAllByGalleryQueryBuilder(Gallery $gallery): QueryBuilder//, int $limit = 25): QueryBuilder
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.gallery', 'g')
            ->andWhere('i.gallery = :gallery')
            ->setParameter('gallery', $gallery)
            ->orderBy('g.id', Criteria::DESC);
//            ->setMaxResults($limit)
//            ->orderBy('i.position', 'ASC');
    }

    /**
     * Query all records by gallery.
     *
     * @param Gallery $gallery Gallery entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByGallery(Gallery $gallery): QueryBuilder
    {
        return $this->queryAll()
            ->select(
                'partial image.{id, title, description, path}',
            )
            ->where('image.gallery = :galleryId')
            ->setParameter('galleryId', $gallery->getId());
    }

    public function findLastFromGallery(Gallery $id)
    {
        return $this->createQueryBuilder('gi')
            ->andWhere('gi.gallery = :gallery')
            ->setParameter('gallery', $id)
            ->orderBy('gi.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param int $limit
     *
     * @return float|int|mixed|string
     */
    public function findFreshImagesLimit(int $limit)
    {
        return $this->createQueryBuilder('gi')
            ->where('gi.visible = :isVisible')
            ->setParameter('isVisible', true)
            ->setMaxResults($limit)
            ->orderBy('gi.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get matches by complex filter criteria -
     * a custom query based on various parameters passed
     *
     * @param $params
     * @return array
     */
    public function findFiltered($params)
    {
        $query = $this->createQueryBuilder('gi')
            ->leftJoin('User:User', 'u', 'WITH', 'gi.user = u.id')
            ->leftJoin('Gallery:Gallery', 'g', 'WITH', 'gi.gallery = g.id')

            ->orderBy('gi.createdAt', self::ORDER_DESCENDING);

        if (key_exists('date_from', $params)) {
            $query->andWhere('gi.createdAt >= :date_from')
                ->setParameter('date_from', $params['date_from']);
        }

        if (key_exists('date_to', $params)) {
            $query->andWhere('gi.createdAt <= :date_to')
                ->setParameter('date_to', $params['date_to']);
        }

        if (key_exists('tournament', $params)) {
            $query->andWhere('m.tournamentId = :tournament_id')
                ->setParameter('tournament_id', $params['tournament']);
        }

        if (key_exists('team', $params)) {
            $query->andWhere($query->expr()->orX(
                $query->expr()->like('h_tm.name', ':team_string'),
                $query->expr()->like('a_tm.name', ':team_string')
            ))
                ->setParameter('team_string', '%' . $params['team'] . '%');
        }

        return $query->getQuery()->getResult();
    }
}
