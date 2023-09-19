<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Talav\Component\User\Model\UserInterface;

class AccessVoter extends AbstractVoter
{
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return ($user instanceof UserInterface) && $this->can($user, $attribute);
    }
}
