<?php

declare(strict_types=1);

namespace Talav\UserBundle\Security\RoleChecker;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Talav\Component\User\Model\UserInterface;

/**
 * Interface RoleCheckerInterface
 * Checks user role by using role hierarchy.
 *
 * @package UserBundle\Utils\RoleChecker
 */
interface RoleCheckerInterface
{

    /**
     * Checks that specified user has necessary role.
     *
     * @param UserInterface                 $user A checked User entity instance.
     * @param string|RoleInterface $role A role name or Role instance.
     *
     * @return boolean
     */
    public function has(UserInterface $user, $role);

    /**
     * Checks that specified user has given role or lower.
     *
     * @param UserInterface                 $user A checked User entity instance.
     * @param string|RoleInterface $role A role name or Role instance.
     *
     * @return boolean
     */
    public function hasNotHigherThen(UserInterface $user, $role);
}
