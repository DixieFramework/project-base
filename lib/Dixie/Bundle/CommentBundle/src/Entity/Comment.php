<?php

declare(strict_types=1);

namespace Talav\CommentBundle\Entity;

use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\User\Model\UserInterface;
use Talav\CommentBundle\Repository\CommentRepository;
use Talav\PostBundle\Entity\PostInterface;
use Talav\ProfileBundle\Entity\LikeInterface;
use Talav\ProfileBundle\Entity\NotificationInterface;
use Talav\ProfileBundle\Entity\ReportInterface;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table('com_comment')]
#[ORM\HasLifecycleCallbacks]
#[ORM\MappedSuperclass]
abstract class Comment implements CommentInterface
{
    use ResourceTrait;
//    #[ORM\Id]
//    #[ORM\GeneratedValue]
//    #[ORM\Column(type: 'integer')]
//    protected $id;

    protected ?string $type = null;

    protected ?string $entityId = null;

//    #[ORM\Column(type: 'text')]
    protected ?string $message = null;

//    #[ORM\Column(type: 'datetimetz_immutable')]
    protected ?\DateTimeImmutable $publishedAt = null;

//    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'comments')]
//    #[ORM\JoinColumn(nullable: false)]
    protected UserInterface $author;

//    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'replies')]
    protected ?UserInterface $replyTo;

//    #[ORM\OneToMany(mappedBy: 'comment', targetEntity: NotificationInterface::class, orphanRemoval: true)]
    protected Collection $notifications;

//    #[ORM\ManyToOne(targetEntity: PostInterface::class, inversedBy: 'comments')]
    protected ?PostInterface $post;

//    #[ORM\Column(type: 'boolean')]
    protected ?bool $status = true;

//    #[ORM\ManyToOne(targetEntity: CommentInterface::class, inversedBy: 'children')]
    protected ?CommentInterface $parent;

//    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: CommentInterface::class, orphanRemoval: true)]
    protected Collection $children;

//    #[ORM\OneToMany(mappedBy: 'comment', targetEntity: LikeInterface::class, orphanRemoval: true)]
    protected Collection $likes;

//    #[ORM\OneToOne(mappedBy: 'comment', targetEntity: ReportInterface::class, cascade: ['persist', 'remove'])]
    protected ?ReportInterface $report;

    #[Pure] public function __construct()
    {
        $this->notifications = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function initialize()
    {
        $this->status = true;
        $this->publishedAt = new \DateTimeImmutable('now');
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     *
     * @return self
     */
    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getEntityId(): ?string
    {
        return $this->entityId;
    }

    /**
     * @param string|null $entityId
     *
     * @return self
     */
    public function setEntityId(?string $entityId): self
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

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

    public function getAuthor(): ?UserInterface
    {
        return $this->author;
    }

    public function setAuthor(?UserInterface $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getReplyTo(): ?UserInterface
    {
        return $this->replyTo;
    }

    public function setReplyTo(?UserInterface $replyTo): self
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    /**
     * @return Collection|NotificationInterface[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(NotificationInterface $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setComment($this);
        }

        return $this;
    }

    public function removeNotification(NotificationInterface $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            if ($notification->getComment() === $this) {
                $notification->setComment(null);
            }
        }

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

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?CommentInterface $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChildren(CommentInterface $children): self
    {
        if (!$this->children->contains($children)) {
            $this->children[] = $children;
            $children->setParent($this);
        }

        return $this;
    }

    public function removeChildren(CommentInterface $children): self
    {
        if ($this->children->contains($children)) {
            $this->children->removeElement($children);
            if ($children->getParent() === $this) {
                $children->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LikeInterface[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(LikeInterface $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setComment($this);
        }

        return $this;
    }

    public function removeLike(LikeInterface $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
            if ($like->getComment() === $this) {
                $like->setComment(null);
            }
        }

        return $this;
    }

    public function getReport(): ?ReportInterface
    {
        return $this->report;
    }

    public function setReport(?ReportInterface $report): self
    {
        $this->report = $report;

        // set (or unset) the owning side of the relation if necessary
        $newComment = null === $report ? null : $this;
        if ($report->getComment() !== $newComment) {
            $report->setComment($newComment);
        }

        return $this;
    }

    #[ORM\PrePersist]
    public function prePersist()
    {
        $this->createdAt = $this->createdAt ? $this->createdAt : new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->updatedAt = clone $this->createdAt;
    }

    #[ORM\PreUpdate]
    public function preUpdate()
    {
        $this->updatedAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }
}
