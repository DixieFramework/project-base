<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Talav\SettingsBundle\Context\ScopeContextInterface;
use Talav\Component\Resource\Repository\RepositoryInterface;
use Talav\SettingsBundle\Model\SettingsOwnerInterface;

interface SettingsRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $scope
     */
    public function removeAllByScope(string $scope): void;

    /**
     * @param ScopeContextInterface $scopeContext
     *
     * @return QueryBuilder
     */
    public function findAllByScopeAndOwner(ScopeContextInterface $scopeContext): QueryBuilder;

    /**
     * @param string                      $name
     * @param string                      $scope
     * @param SettingsOwnerInterface|null $owner
     *
     * @return QueryBuilder
     */
    public function findOneByNameAndScopeAndOwner(string $name, string $scope, SettingsOwnerInterface $owner = null): QueryBuilder;

    /**
     * @param string                 $scope
     * @param SettingsOwnerInterface $settingsOwner
     *
     * @return QueryBuilder
     */
    public function findByScopeAndOwner(string $scope, SettingsOwnerInterface $settingsOwner): QueryBuilder;
}
