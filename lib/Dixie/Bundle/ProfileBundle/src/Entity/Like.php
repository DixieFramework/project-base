<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Talav\CommentBundle\Entity\CommentInterface;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\PostBundle\Entity\Comment;
use Talav\PostBundle\Entity\PostInterface;
use Talav\ProfileBundle\Repository\LikeRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Talav\UserBundle\Model\UserInterface;

//#[ORM\Entity(repositoryClass: LikeRepository::class)]
//#[ORM\Table(name: 'like')]
#[ORM\HasLifecycleCallbacks]
#[ORM\MappedSuperclass]
class Like implements LikeInterface
{
	use ResourceTrait;

    #[ORM\Column(name: 'liked_at', type: 'datetime', columnDefinition: 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP')]
    protected $likedAt;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: false)]
    protected $user;

    #[ORM\ManyToOne(targetEntity: PostInterface::class, inversedBy: 'likes')]
    protected $post;

    #[ORM\ManyToOne(targetEntity: CommentInterface::class, inversedBy: 'likes')]
    protected $comment;

    #[ORM\PrePersist]
    public function initialize()
    {
        $this->likedAt = new DateTime('now');
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

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

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

    public function getComment(): ?CommentInterface
    {
        return $this->comment;
    }

    public function setComment(?CommentInterface $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
