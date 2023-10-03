<?php

declare(strict_types=1);

namespace Talav\PostBundle\Entity;

use Talav\PostBundle\Repository\BookmarkRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Talav\UserBundle\Model\UserInterface;

#[ORM\Entity(repositoryClass: BookmarkRepository::class)]
#[ORM\Table('bookmark')]
#[ORM\HasLifecycleCallbacks]
class Bookmark
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: UserInterface::class, inversedBy: 'bookmarks')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\ManyToOne(targetEntity: PostInterface::class, inversedBy: 'bookmarks')]
    #[ORM\JoinColumn(nullable: false)]
    private $post;

    #[ORM\Column(type: 'datetime')]
    private $addedAt;

    #[ORM\PrePersist]
    public function initialize()
    {
        $this->addedAt = new DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAddedAt(): ?\DateTimeInterface
    {
        return $this->addedAt;
    }

    public function setAddedAt(\DateTimeInterface $addedAt): self
    {
        $this->addedAt = $addedAt;

        return $this;
    }
}
