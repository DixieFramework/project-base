<?php

declare(strict_types=1);

namespace Talav\PostBundle\Entity;

use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\PostBundle\Repository\CommentRepository;
use Talav\ProfileBundle\Entity\LikeInterface;
use Talav\ProfileBundle\Entity\NotificationInterface;
use Talav\ProfileBundle\Entity\Report;
use Talav\ProfileBundle\Entity\ReportInterface;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table('comment')]
#[ORM\HasLifecycleCallbacks]
class Comment implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected $id;

    #[ORM\Column(type: 'text')]
    protected $message;

    #[ORM\Column(type: 'datetime')]
    protected $publishedAt;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    protected $author;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'replies')]
    protected $replyTo;

    #[ORM\OneToMany(mappedBy: 'comment', targetEntity: NotificationInterface::class, orphanRemoval: true)]
    protected $notifications;

    #[ORM\ManyToOne(targetEntity: PostInterface::class, inversedBy: 'comments')]
    protected $post;

    #[ORM\Column(type: 'boolean')]
    protected $status;

    #[ORM\ManyToOne(targetEntity: Comment::class, inversedBy: 'children')]
    protected $parent;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Comment::class, orphanRemoval: true)]
    protected $children;

    #[ORM\OneToMany(mappedBy: 'comment', targetEntity: LikeInterface::class, orphanRemoval: true)]
    protected $likes;

    #[ORM\OneToOne(mappedBy: 'comment', targetEntity: ReportInterface::class, cascade: ['persist', 'remove'])]
    protected $report;

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
        $this->publishedAt = new \DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getSong(): ?Song
    {
        return $this->song;
    }

    public function setSong(?Song $song): self
    {
        $this->song = $song;

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

    public function getReplyTo(): ?User
    {
        return $this->replyTo;
    }

    public function setReplyTo(?User $replyTo): self
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setComment($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            if ($notification->getComment() === $this) {
                $notification->setComment(null);
            }
        }

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

    public function setParent(?self $parent): self
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

    public function addChildren(self $children): self
    {
        if (!$this->children->contains($children)) {
            $this->children[] = $children;
            $children->setParent($this);
        }

        return $this;
    }

    public function removeChildren(self $children): self
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
     * @return Collection|Like[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setComment($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
            if ($like->getComment() === $this) {
                $like->setComment(null);
            }
        }

        return $this;
    }

    public function getReport(): ?Report
    {
        return $this->report;
    }

    public function setReport(?Report $report): self
    {
        $this->report = $report;

        // set (or unset) the owning side of the relation if necessary
        $newComment = null === $report ? null : $this;
        if ($report->getComment() !== $newComment) {
            $report->setComment($newComment);
        }

        return $this;
    }
}
