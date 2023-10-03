<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Model;

use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\TimestampableInterface;

interface SettingsInterface extends ResourceInterface, TimestampableInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     */
    public function setName(string $name);

    /**
     * @return string
     */
    public function getScope(): string;

    /**
     * @param string $scope
     */
    public function setScope(string $scope);

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param mixed $value
     */
    public function setValue($value);

    /**
     * @return int
     */
    public function getOwner();

    /**
     * @param int $owner
     */
    public function setOwner(int $owner);
}
