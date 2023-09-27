<?php

namespace Talav\ProfileBundle\Entity;

use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Entity\Interfaces\EntityInterface;
use Talav\CoreBundle\Entity\Traits\ComparisonTrait;
use Talav\CoreBundle\Entity\Traits\IdentifierTrait;
use Talav\CoreBundle\Entity\Traits\TimestampTrait;
use Talav\ProfileBundle\Enum\FriendStatus;
use Talav\ProfileBundle\Repository\FriendRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: FriendRepository::class)]
#[ORM\Table('user_friend')]
#[ORM\HasLifecycleCallbacks]
class Friend implements ResourceInterface, EntityInterface
{
	use ResourceTrait;
//    use IdentifierTrait;
//    use TimestampTrait;
    use ComparisonTrait;

//    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'friendRequests')]
//    #[ORM\JoinColumn(nullable: false)]
	protected ?UserInterface $user = null;

//    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'acceptedFriendRequests')]
//    #[ORM\JoinColumn(nullable: false)]
	protected ?UserInterface $friend = null;

//    #[ORM\Column(type: Types::STRING, length: 255, options: [
//        'comment' => 'Status from FriendStatus'
//    ])]
	protected ?string $status = null;

	protected ?\DateTimeImmutable $createdAt;

	protected ?\DateTimeImmutable $updatedAt;

	public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFriend(): ?UserInterface
    {
        return $this->friend;
    }

    public function setFriend(?UserInterface $friend): self
    {
        $this->friend = $friend;

        return $this;
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

	public function getCreatedAt(): ?\DateTimeImmutable
	{
		return $this->createdAt;
	}

	public function setCreatedAt(?\DateTimeImmutable $createdAt): self
	{
		$this->createdAt = $createdAt;

		return $this;
	}

	public function getUpdatedAt(): ?\DateTimeImmutable
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
	{
		$this->updatedAt = $updatedAt;

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
}
