<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Context;

use Talav\SettingsBundle\Model\SettingsOwnerInterface;

abstract class AbstractScopeContext implements ScopeContextInterface
{
    protected $scopeOwners = [];

    /**
     * {@inheritdoc}
     */
    public function getScopesOwners(): array
    {
        return $this->scopeOwners;
    }

    /**
     * {@inheritdoc}
     */
    public function setScopeOwner(string $scope, SettingsOwnerInterface $owner)
    {
        if (\in_array($scope, $this->getScopes(), true)) {
            $this->scopeOwners[$scope] = $owner;

            return $owner;
        }
    }

    /**
     * @param string $scope
     *
     * @return SettingsOwnerInterface|bool
     */
    public function getScopeOwner(string $scope)
    {
        if (array_key_exists($scope, $this->scopeOwners)) {
            return $this->scopeOwners[$scope];
        }

        return null;
    }
}
