<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Entity\Interfaces\EntityInterface;
use Talav\CoreBundle\Entity\Traits\ComparisonTrait;
use Talav\CoreBundle\Entity\Traits\IdentifierTrait;
use Talav\CoreBundle\Entity\Traits\TimestampTrait;
use Talav\ProfileBundle\Enum\FriendStatus;
use Talav\ProfileBundle\Repository\UserFriendRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

//#[ORM\Table]
//#[ORM\Entity(repositoryClass: UserFriendRepository::class)]
#[ORM\HasLifecycleCallbacks]
abstract class UserFriend implements ResourceInterface
{
    use ResourceTrait;
//    #[ORM\Id]
//    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
//    #[ORM\Column(type: 'integer', nullable: false)]
//    protected ?int $id = null;

//    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'friends')]
//    #[ORM\JoinColumn(referencedColumnName: 'id', nullable: false)]
    protected UserInterface $user;

//    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'friended')]
//    #[ORM\JoinColumn(referencedColumnName: 'id', nullable: false)]
    protected UserInterface $friend;

    protected ?string $status = null;

//    #[ORM\Column(type: 'datetime', nullable: false)]
    protected ?\DateTime $createdAt = null;

//    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTime $updatedAt = null;

//    public function getId(): ?int
//    {
//        return $this->id;
//    }

    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setFriend(UserInterface $friend): self
    {
        $this->friend = $friend;

        return $this;
    }

    public function getFriend(): UserInterface
    {
        return $this->friend;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?FriendStatus $status): self
    {
        $this->status = $status?->name;

        return $this;
    }

    public function setAwaitingConfirmation(): self
    {
        return $this->setStatus(FriendStatus::AWAITING_CONFIRMATION);
    }

    #[Pure]
    public function isAwaitingConfirmation(): bool
    {
        return $this->getStatus() === FriendStatus::AWAITING_CONFIRMATION->name;
    }

    public function setConfirmed(): self
    {
        return $this->setStatus(FriendStatus::CONFIRMED);
    }

    #[Pure]
    public function isConfirmed(): bool
    {
        return $this->getStatus() === FriendStatus::CONFIRMED->name;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): self
    {
        $this->createdAt = new \DateTime();

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): self
    {
        $this->updatedAt = new \DateTime();

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->getFriend();
    }
}
