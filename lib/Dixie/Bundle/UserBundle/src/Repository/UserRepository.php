<?php

declare(strict_types=1);

namespace Talav\UserBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Talav\Component\User\Repository\UserRepository as TalavUserRepository;

class UserRepository extends TalavUserRepository
{
    public function findUserByIdentifier($identifier)
    {
        $qb = $this->getUserWithPermissionsQuery()
            ->where('u.emailCanonical=:email')
            ->orWhere('u.usernameCanonical=:username');

        $query = $qb->getQuery();
        $query->setParameter('email', $identifier);
        $query->setParameter('username', $identifier);

        return $query->getSingleResult();
    }

    public function findUserWithSecurityById($id)
    {
        $queryBuilder = $this->getUserWithPermissionsQuery()
            ->where('u.id = :id');

        $query = $queryBuilder->getQuery();
        $query->setParameter('id', $id);

        return $query->getSingleResult();
    }

    /**
     * Returns the query for the user with the joins for retrieving the permissions. Especially useful for security
     * related queries.
     *
     * @return QueryBuilder
     */
    private function getUserWithPermissionsQuery()
    {
        return $this->createQueryBuilder('u')
            ->addSelect('roles')
            ->addSelect('permissions')
            ->leftJoin('u.userRoles', 'roles')
            ->leftJoin('roles.rolePermissions', 'permissions');
    }
}
