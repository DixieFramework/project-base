<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Talav\CommentBundle\Entity\CommentInterface;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\PostBundle\Entity\PostInterface;
use Talav\UserBundle\Model\UserInterface;

interface LikeInterface extends ResourceInterface
{
    public function getLikedAt(): ?\DateTimeInterface;

    public function setLikedAt(\DateTimeInterface $likedAt): self;

    public function getUser(): ?UserInterface;

    public function setUser(?UserInterface $user): self;

    public function getPost(): ?PostInterface;

    public function setPost(?PostInterface $post): self;

    public function getComment(): ?CommentInterface;

    public function setComment(?CommentInterface $comment): self;
}
