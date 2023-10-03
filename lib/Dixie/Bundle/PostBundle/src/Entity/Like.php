<?php

declare(strict_types=1);

namespace Talav\PostBundle\Entity;

use Talav\PostBundle\Repository\LikeRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Talav\UserBundle\Model\UserInterface;

#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\Table(name: '`like`')]
#[ORM\HasLifecycleCallbacks]
class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $likedAt;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'likes')]
    private $post;

    #[ORM\ManyToOne(targetEntity: Comment::class, inversedBy: 'likes')]
    private $comment;

    #[ORM\PrePersist]
    public function initialize()
    {
        $this->likedAt = new DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLikedAt(): ?\DateTimeInterface
    {
        return $this->likedAt;
    }

    public function setLikedAt(\DateTimeInterface $likedAt): self
    {
        $this->likedAt = $likedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(?Comment $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
