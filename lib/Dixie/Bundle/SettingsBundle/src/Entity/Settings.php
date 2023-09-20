<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\Resource\Model\Timestampable;
use Talav\CoreBundle\Doctrine\Entity\Behavior\TimestampableInterface;
use Talav\CoreBundle\Doctrine\Entity\Behavior\TimestampableTrait;
use Talav\CoreBundle\Doctrine\Entity\Traits\CreatedAtTrait;
use Talav\CoreBundle\Doctrine\Entity\Traits\MixedIdTrait;
use Talav\CoreBundle\Doctrine\Entity\Traits\UpdatedAtTrait;
use Talav\SettingsBundle\Model\SettingsInterface;

#[ORM\MappedSuperclass]
abstract class Settings implements SettingsInterface
{
	use MixedIdTrait;
    use TimestampableTrait;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    protected $name;

    /**
     * @var string
     */
    #[ORM\Column(type: 'text')]
    protected $scope;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    protected $value;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    protected $owner;

    /**
     * Settings constructor.
     */
    public function __construct()
    {
        $this->setCreatedAt();
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
