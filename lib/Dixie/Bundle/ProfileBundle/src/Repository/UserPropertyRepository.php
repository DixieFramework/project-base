<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Repository;

use Talav\Component\Resource\Repository\RepositoryInterface;
use Talav\CoreBundle\Repository\AbstractRepository;
use Talav\ProfileBundle\Entity\UserProperty;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Repository for user's property entity.
 *
 * @template-extends AbstractRepository<UserProperty>
 */
class UserPropertyRepository extends AbstractRepository implements RepositoryInterface
{
//    public function __construct(ManagerRegistry $registry)
//    {
//        parent::__construct($registry, UserProperty::class);
//    }

    /**
     * Gets all properties for the given user.
     *
     * @return UserProperty[] the user's properties
     */
    public function findByUser(UserInterface $user): array
    {
        return $this->findBy(['user' => $user]);
    }

    /**
     * Gets a property for the given user and name.
     */
    public function findOneByUserAndName(UserInterface $user, string $name): ?UserProperty
    {
        return $this->findOneBy(['user' => $user, 'name' => $name]);
    }
}
