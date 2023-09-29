<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Talav\PermissionBundle\Security\PermissionStorageInterface;

class RolePermissionVoter extends Voter implements VoterInterface
{
    private PermissionStorageInterface $permissionStorage;

    public function __construct(PermissionStorageInterface $permissionStorage)
    {
        $this->permissionStorage = $permissionStorage;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        return in_array($attribute, $this->permissionStorage->getPermissions(), true);
    }
}
