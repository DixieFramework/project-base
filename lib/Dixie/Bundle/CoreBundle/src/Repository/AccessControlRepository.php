<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Repository;

use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Entity\AccessControl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AccessControl|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccessControl|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccessControl[]    findAll()
 * @method AccessControl[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessControlRepository extends ServiceEntityRepository
{
    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';
    const ACTIVE = 1;
    const NOT_ACTIVE = 0;


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessControl::class);
    }

    public function findActive($active = true)
    {
        return parent::findBy(['active' => $active], ['sort' => self::SORT_ASC]);
    }

    /**
     * Find routes by user roles
     * Note that if you are SuperAdmin, you will get all routes!
     *
     * @param UserInterface $user user with roles
     * @param bool $active if you need only active routes (true by default)
     */
    public function findByRole($user, $active = true)
    {
        $allRoutes = $this->findActive();

        $roles = $user->getRoles();
        $roles = \array_diff($roles, ['ROLE_USER']);

        if ( in_array('ROLE_SuperAdmin', $roles)) {
            return $allRoutes;
        }

        $permittedRoutes = [];

        foreach($allRoutes as $route) {
            $acRoles = $route->getRoles();

            if (
                count(\array_intersect($roles, $acRoles )) > 0 ||
                count(\array_intersect(['IS_AUTHENTICATED_FULLY'], $acRoles )) > 0
            ) {
                $permittedRoutes[] = $route;
            }
        }

        return $permittedRoutes;
    }
}
