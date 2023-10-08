<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Repository;

use Talav\Component\Resource\Repository\ResourceRepository;
use Talav\Component\User\Model\UserInterface;
use Talav\ProfileBundle\Entity\ProfileBlock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Talav\ProfileBundle\Model\ProfileInterface;

/**
 * @extends ServiceEntityRepository<ProfileBlock>
 *
 * @method ProfileBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfileBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProfileBlock[]    findAll()
 * @method ProfileBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfileBlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfileBlock::class);
    }

    public function findUserBlocksIds(ProfileInterface $profile): array
    {
        return array_column(
            $this->createQueryBuilder('pb')
                ->select('pbu.id')
                ->join('pb.blocked', 'pbu')
                ->where('pb.blocker = :profile')
                ->setParameter('profile', $profile)
                ->getQuery()
                ->getResult(),
            'id'
        );
    }
}
