<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Doctrine\ORM\Mapping\Cache;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Talav\CoreBundle\Doctrine\Entity\Traits\CreatedAtTzTrait;
use Talav\ProfileBundle\Model\ProfileInterface;

#[Entity(repositoryClass: 'Talav\ProfileBundle\Repository\ProfileBlockRepository')]
#[Table('profile_block')]
#[UniqueConstraint(name: 'profile_block_idx', columns: ['blocker_id', 'blocked_id'])]
#[Cache(usage: 'NONSTRICT_READ_WRITE')]
class ProfileBlock
{
    use CreatedAtTzTrait {
        CreatedAtTzTrait::__construct as createdAtTzTraitConstruct;
    }

    #[Id]
    #[GeneratedValue(strategy: 'IDENTITY')]
    #[Column(type: 'integer')]
    protected int $id;

    #[ManyToOne(targetEntity: ProfileInterface::class, inversedBy: 'blocks')]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public ?ProfileInterface $blocker;

    #[ManyToOne(targetEntity: ProfileInterface::class, inversedBy: 'blockers')]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public ?ProfileInterface $blocked;



    public function __construct(ProfileInterface $blocker, ProfileInterface $blocked)
    {
        $this->createdAtTzTraitConstruct();

        $this->blocker = $blocker;
        $this->blocked = $blocked;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __sleep()
    {
        return [];
    }
}
