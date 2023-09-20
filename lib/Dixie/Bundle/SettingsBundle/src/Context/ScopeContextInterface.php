<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Context;

use Talav\SettingsBundle\Model\SettingsOwnerInterface;

interface ScopeContextInterface
{
    const SCOPE_GLOBAL = 'global';

    const SCOPE_USER = 'user';

    public function getScopes(): array;

    public function getScopesOwners(): array;

    /**
     * @return null|SettingsOwnerInterface
     */
    public function setScopeOwner(string $scope, SettingsOwnerInterface $owner);

    /**
     * @return null|SettingsOwnerInterface
     */
    public function getScopeOwner(string $scope);
}
