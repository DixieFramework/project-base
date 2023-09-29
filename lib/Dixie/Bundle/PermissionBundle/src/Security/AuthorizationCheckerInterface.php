<?php

declare(strict_types=1);

namespace Talav\PermissionBundle\Security;

interface AuthorizationCheckerInterface
{
    /**
     * Checks if the attribute is granted against the current authentication token and optionally supplied subject.
     *
     * @param mixed $attribute A single attribute to vote on (can be of any type)
     *
     */
    public function isGranted(mixed $attribute, mixed $subject = null): bool;
}
