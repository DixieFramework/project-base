<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Talav\Component\User\Model\UserInterface;
use Talav\ProfileBundle\Repository\UserProfileRelationRepository;

#[ORM\Entity(repositoryClass: UserProfileRelationRepository::class)]
#[ORM\Table('user_profile_relation')]
class UserProfileRelation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 30)]
    private $status = "pending";

    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'sendedUserRelations')]
    #[ORM\JoinColumn(nullable: false)]
    private $sender;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'receivedUserRelations')]
    #[ORM\JoinColumn(nullable: false)]
    private $recipient;

    #[ORM\Column(type: 'string', length: 30, nullable: true)]
    private $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getSender(): ?UserInterface
    {
        return $this->sender;
    }

    public function setSender(?UserInterface $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getRecipient(): ?UserInterface
    {
        return $this->recipient;
    }

    public function setRecipient(?UserInterface $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
