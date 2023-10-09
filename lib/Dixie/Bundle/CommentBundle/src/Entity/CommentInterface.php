<?php

declare(strict_types=1);

namespace Talav\CommentBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\PostBundle\Entity\PostInterface;
use Talav\ProfileBundle\Entity\LikeInterface;
use Talav\ProfileBundle\Entity\NotificationInterface;
use Talav\ProfileBundle\Entity\ReportInterface;

interface CommentInterface extends ResourceInterface
{
    public function getMessage(): ?string;

    public function setMessage(string $message): self;

    public function getPublishedAt(): ?\DateTimeImmutable;

    public function setPublishedAt(\DateTimeImmutable $publishedAt): self;

    public function getAuthor(): ?UserInterface;

    public function setAuthor(?UserInterface $author): self;

    public function getReplyTo(): ?UserInterface;

    public function setReplyTo(?UserInterface $replyTo): self;

    /**
     * @return Collection|NotificationInterface[]
     */
    public function getNotifications(): Collection;

    public function addNotification(NotificationInterface $notification): self;

    public function removeNotification(NotificationInterface $notification): self;

    public function getPost(): ?PostInterface;

    public function setPost(?PostInterface $post): self;

    public function getStatus(): ?bool;

    public function setStatus(bool $status): self;

    public function getParent(): ?self;

    public function setParent(?self $parent): self;

    /**
     * @return Collection|self[]
     */
    public function getChildren(): Collection;

    public function addChildren(self $children): self;

    public function removeChildren(self $children): self;

    /**
     * @return Collection|LikeInterface[]
     */
    public function getLikes(): Collection;

    public function addLike(LikeInterface $like): self;

    public function removeLike(LikeInterface $like): self;

    public function getReport(): ?ReportInterface;

    public function setReport(?ReportInterface $report): self;
}
