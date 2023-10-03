<?php

declare(strict_types=1);

namespace Talav\PostBundle\Entity;

use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\PostBundle\Repository\LikeRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Talav\UserBundle\Model\UserInterface;

//#[ORM\Entity(repositoryClass: LikeRepository::class)]
//#[ORM\Table(name: 'like')]
#[ORM\HasLifecycleCallbacks]
#[ORM\MappedSuperclass]
abstract class Like implements ResourceInterface
{
	use ResourceTrait;

    #[ORM\Column(type: 'datetime')]
    protected $likedAt;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: false)]
    protected $user;

    #[ORM\ManyToOne(targetEntity: PostInterface::class, inversedBy: 'likes')]
    protected $post;

    #[ORM\ManyToOne(targetEntity: Comment::class, inversedBy: 'likes')]
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
