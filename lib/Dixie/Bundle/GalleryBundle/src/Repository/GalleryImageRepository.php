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

    /**
     * Save image.
     *
     * @param GalleryImage $entity Image entity
     * @param bool         $flush  If perform flush
     */
    public function save(GalleryImage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Delete image.
     *
     * @param GalleryImage $entity Image entity
     * @param bool         $flush  If perform flush
     */
    public function delete(GalleryImage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
