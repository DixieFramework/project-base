<?php

declare(strict_types=1);

namespace Talav\GalleryBundle\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Talav\Component\Resource\Repository\ResourceRepository;
use Talav\Component\User\Model\UserInterface;
use Talav\GalleryBundle\Entity\Gallery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class GalleryRepository.
 *
 * @extends ServiceEntityRepository<Gallery>
 *
 * @method Gallery|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gallery|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gallery[]    findAll()
 * @method Gallery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
//class GalleryRepository extends ServiceEntityRepository
class GalleryRepository extends ResourceRepository
{
    public const PAGINATOR_ITEMS_PER_PAGE = 5;

//    /**
//     * Constructor.
//     *
//     * @param ManagerRegistry $registry Object manager
//     */
//    public function __construct(ManagerRegistry $registry)
//    {
//        parent::__construct($registry, Gallery::class);
//    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('gallery')
            ->orderBy('gallery.id', 'DESC');
    }

    public function findAllByUserQueryBuilder(UserInterface $user): QueryBuilder
    {
        return $this
            ->createQueryBuilder('g')
            ->where('g.user = :user')
            ->setParameters(['user' => $user])
            ->orderBy('g.id', Criteria::DESC);
    }
}
