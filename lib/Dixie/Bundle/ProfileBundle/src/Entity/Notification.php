<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\ProfileBundle\Repository\NotificationRepository;
use Talav\UserBundle\Model\UserInterface;
use Talav\PostBundle\Entity\Comment;

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
    protected $seen;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'sentNotifications')]
    protected $sender;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'receivedNotifications')]
    #[ORM\JoinColumn(nullable: false)]
    protected $receiver;

    #[ORM\ManyToOne(targetEntity: Comment::class, inversedBy: 'notifications')]
    protected $comment;

    #[ORM\Column(type: 'datetime')]
    protected $publishedAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected $message;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected $type;

    #[ORM\Column(type: 'boolean')]
    protected $status;

//    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'notifications')]
//    protected $post;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'notifications')]
    protected UserInterface $user;

    #[ORM\Column(type: 'integer', nullable: true)]
    protected $quantity;

//    #[ORM\ManyToOne(targetEntity: Follow::class, inversedBy: 'notifications')]
//    protected $follow;

    #[ORM\PrePersist]
    public function initialize(): void
    {
        $this->seen = false;
        $this->status = true;
        $this->publishedAt = new \DateTime('now');
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

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(?Comment $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeInterface $publishedAt): self
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

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
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
