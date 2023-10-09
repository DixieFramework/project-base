<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Talav\CommentBundle\Entity\CommentInterface;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\PostBundle\Entity\Comment;
use Talav\ProfileBundle\Entity\MessageInterface;
use Talav\ProfileBundle\Repository\ReportRepository;
use Doctrine\ORM\Mapping as ORM;
use Talav\UserBundle\Model\UserInterface;

//#[ORM\Entity(repositoryClass: ReportRepository::class)]
//#[ORM\Table('report')]
#[ORM\HasLifecycleCallbacks]
class Report implements ReportInterface
{
    use ResourceTrait;

    #[ORM\OneToOne(targetEntity: MessageInterface::class, cascade: ['persist', 'remove'])]
    protected ?MessageInterface $message;

    #[ORM\OneToOne(targetEntity: UserInterface::class, cascade: ['persist', 'remove'])]
    protected ?UserInterface $profile;

    #[ORM\OneToOne(inversedBy: 'report', targetEntity: CommentInterface::class, cascade: ['persist', 'remove'])]
    protected ?CommentInterface $comment;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $content;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    protected UserInterface $sender;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'accusations')]
    #[ORM\JoinColumn(nullable: false)]
    protected UserInterface $accused;

    #[ORM\Column(type: 'boolean')]
    protected ?bool $seen = false;

    #[ORM\PrePersist]
    public function initialize()
    {
        $this->seen = false;
    }

    public function getMessage(): ?MessageInterface
    {
        return $this->message;
    }

    public function setMessage(?MessageInterface $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getProfile(): ?UserInterface
    {
        return $this->profile;
    }

    public function setProfile(?UserInterface $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(?Comment $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

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

    public function getAccused(): ?UserInterface
    {
        return $this->accused;
    }

    public function setAccused(?UserInterface $accused): self
    {
        $this->accused = $accused;

        return $this;
    }

    public function getSeen(): ?bool
    {
        return $this->seen;
    }

    public function setSeen(bool $seen): self
    {
        $this->seen = $seen;

        return $this;
    }
}
