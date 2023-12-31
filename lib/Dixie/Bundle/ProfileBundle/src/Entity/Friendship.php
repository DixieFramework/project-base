<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\ProfileBundle\Entity\ProfileInterface;
use Talav\ProfileBundle\Repository\FriendshipRepository;
use Doctrine\ORM\Mapping as ORM;

//#[ORM\Entity(repositoryClass: FriendshipRepository::class)]
#[ORM\MappedSuperclass]
class Friendship implements FriendshipInterface
{
	use ResourceTrait;
//    #[ORM\Id]
//    #[ORM\GeneratedValue]
//    #[ORM\Column]
//    private ?int $id = null;

//    #[ORM\ManyToOne(inversedBy: 'friendships')]
//    #[ORM\JoinColumn(nullable: false)]
    protected ?ProfileInterface $profile = null;

//    #[ORM\ManyToOne]
//    #[ORM\JoinColumn(nullable: false)]
	protected ?ProfileInterface $friend = null;

	protected ?\DateTime $createdAt = null;

	protected ?\DateTime $updatedAt = null;

//    public function getId(): ?int
//    {
//        return $this->id;
//    }

    public function getProfile(): ?ProfileInterface
    {
        return $this->profile;
    }

    public function setProfile(?ProfileInterface $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function getFriend(): ?ProfileInterface
    {
        return $this->friend;
    }

    public function setFriend(?ProfileInterface $friend): self
    {
        $this->friend = $friend;

        return $this;
    }

	public function getCreatedAt(): ?\DateTime
	{
		return $this->createdAt;
	}

	#[ORM\PrePersist]
	public function setCreatedAt(): self
	{
		$this->createdAt = new \DateTime();

		return $this;
	}

	public function getUpdatedAt(): ?\DateTime
	{
		return $this->updatedAt;
	}

	#[ORM\PreUpdate]
	public function setUpdatedAt(): self
	{
		$this->updatedAt = new \DateTime();

		return $this;
	}
}
