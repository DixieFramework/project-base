<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\Resource\Model\TimestampableTrait;
use Talav\SettingsBundle\Model\SettingsInterface;

abstract class Settings implements SettingsInterface
{
	use ResourceTrait;
    use TimestampableTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $scope;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var int
     */
    protected $owner;

    /**
     * Settings constructor.
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * {@inheritdoc}
     */
    public function setScope(string $scope)
    {
        $this->scope = $scope;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * {@inheritdoc}
     */
    public function setOwner(int $owner)
    {
        $this->owner = $owner;
    }
}
