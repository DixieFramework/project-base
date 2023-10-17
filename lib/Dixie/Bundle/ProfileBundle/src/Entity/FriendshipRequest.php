<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\ProfileBundle\Entity\ProfileInterface;
use Talav\ProfileBundle\Repository\FriendshipRequestRepository;
use Doctrine\ORM\Mapping as ORM;

//#[ORM\Entity(repositoryClass: FriendshipRequestRepository::class)]
class FriendshipRequest implements ResourceInterface
{
	use ResourceTrait;
//    #[ORM\Id]
//    #[ORM\GeneratedValue]
//    #[ORM\Column]
//    private ?int $id = null;

//    #[ORM\ManyToOne(inversedBy: 'requester')]
//    #[ORM\JoinColumn(nullable: false)]
	protected ?ProfileInterface $requester = null;

//    #[ORM\ManyToOne(inversedBy: 'requestee')]
//    #[ORM\JoinColumn(nullable: false)]
	protected ?ProfileInterface $requestee = null;

	protected ?\DateTime $createdAt = null;

	protected ?\DateTime $updatedAt = null;

//    public function getId(): ?int
//    {
//        return $this->id;
//    }

    public function getRequester(): ?ProfileInterface
    {
        return $this->requester;
    }

    public function setRequester(?ProfileInterface $requester): self
    {
        $this->requester = $requester;

        return $this;
    }

    public function getRequestee(): ?ProfileInterface
    {
        return $this->requestee;
    }

    public function setRequestee(?ProfileInterface $requestee): self
    {
        $this->requestee = $requestee;

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
