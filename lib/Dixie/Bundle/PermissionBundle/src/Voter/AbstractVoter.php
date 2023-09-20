<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Voter;

use Talav\Component\User\Model\UserInterface;
use Talav\PermissionBundle\Entity\PermissionInterface;
use Talav\PermissionBundle\Repository\PermissionRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class AbstractVoter extends Voter
{
    public function __construct(
        private PermissionRepositoryInterface $permissionRepository
    ){}

    protected function supports(string $attribute, $subject): bool
    {
        return true; // Supports always.
    }

    public function can(UserInterface $user, string $accessIdentifier): bool
    {
        if ($user->hasRole($accessIdentifier))
            return true;

        /** @var PermissionInterface $permission */
        if ($permission = $this->permissionRepository->findOneBy(['name' => $accessIdentifier])) {
	        if ($user->hasPermissionTo($permission))
		        return true;
        }


        return false;
    }
}
