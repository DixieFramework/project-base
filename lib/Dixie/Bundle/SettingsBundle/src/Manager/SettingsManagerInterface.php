<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Manager;

use Talav\SettingsBundle\Context\ScopeContext;
use Talav\SettingsBundle\Context\ScopeContextInterface;
use Talav\SettingsBundle\Model\SettingsInterface;
use Talav\SettingsBundle\Model\SettingsOwnerInterface;

interface SettingsManagerInterface
{
    /**
     * Returns setting value by its name.
     *
     * @param string                      $name
     * @param string                      $scope
     * @param SettingsOwnerInterface|null $owner
     * @param null                        $default
     *
     * @return mixed
     */
    public function get(string $name, $scope = ScopeContext::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null, $default = null);

    /**
     * Returns all settings as associative name-value array.
     *
     * @return array
     */
    public function all();

    /**
     * Sets setting value by its name.
     *
     * @param string                      $name
     * @param mixed                       $value
     * @param string                      $scope
     * @param SettingsOwnerInterface|null $owner
     *
     * @return SettingsInterface
     */
    public function set(string $name, $value, $scope = ScopeContext::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null);

    /**
     * Clears setting value.
     *
     * @param string                      $name
     * @param string                      $scope
     * @param SettingsOwnerInterface|null $owner
     *
     * @return mixed
     */
    public function clear(string $name, $scope = ScopeContext::SCOPE_GLOBAL, SettingsOwnerInterface $owner = null): bool;

    /**
     * @param $scope
     */
    public function clearAllByScope(string $scope = ScopeContextInterface::SCOPE_GLOBAL): void;

    /**
     * @param string                 $scope
     * @param SettingsOwnerInterface $settingsOwner
     *
     * @return array
     */
    public function getByScopeAndOwner(string $scope, SettingsOwnerInterface $settingsOwner): array;

    /**
     * @param string $name
     *
     * @return array|null
     */
    public function getOneSettingByName(string $name): ?array;
}
