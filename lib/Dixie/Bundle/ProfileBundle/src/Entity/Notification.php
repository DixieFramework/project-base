<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Talav\CommentBundle\Entity\CommentInterface;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\PostBundle\Entity\PostInterface;
use Talav\ProfileBundle\Repository\NotificationRepository;
use Talav\UserBundle\Model\UserInterface;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ORM\Table(name: 'notification')]
#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class Notification implements NotificationInterface
{
	use ResourceTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected mixed $id;

    #[ORM\Column(type: 'boolean')]
    protected ?bool $seen = false;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'sentNotifications')]
    protected ?UserInterface $sender;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'receivedNotifications')]
    #[ORM\JoinColumn(nullable: false)]
    protected UserInterface $receiver;

    #[ORM\ManyToOne(targetEntity: CommentInterface::class, inversedBy: 'notifications')]
    protected ?CommentInterface $comment;

    #[ORM\Column(name: 'published_at', type: 'datetimetz_immutable', columnDefinition: 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP')]
    protected ?\DateTimeImmutable $publishedAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $message;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $type;

    #[ORM\Column(type: 'boolean')]
    protected bool $status = true;

    #[ORM\ManyToOne(targetEntity: PostInterface::class, inversedBy: 'notifications')]
    protected PostInterface $post;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'notifications')]
    protected UserInterface $user;

    #[ORM\Column(type: 'integer', nullable: true)]
    protected ?int $quantity;

//    #[ORM\ManyToOne(targetEntity: Follow::class, inversedBy: 'notifications')]
//    protected $follow;

    #[ORM\PrePersist]
    public function initialize(): void
    {
        $this->seen = false;
        $this->status = true;
        $this->publishedAt = new \DateTimeImmutable('now');
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSender(): ?UserInterface
    {
        return $this->sender;
    }

    public function setSender(?UserInterface $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): ?UserInterface
    {
        return $this->receiver;
    }

    public function setReceiver(?UserInterface $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getComment(): ?CommentInterface
    {
        return $this->comment;
    }

    public function setComment(?CommentInterface $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPost(): ?PostInterface
    {
        return $this->post;
    }

    public function setPost(?PostInterface $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFollow(): ?Follow
    {
        return $this->follow;
    }

    public function setFollow(?Follow $follow): self
    {
        $this->follow = $follow;

        return $this;
    }
}
