<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Talav\Component\Resource\Model\ResourceInterface;
use Talav\UserBundle\Model\UserInterface;

interface NotificationInterface extends ResourceInterface
{
    public function initialize(): void;

    public function getSeen(): ?bool;

    public function setSeen(bool $seen): self;

    public function getSender(): ?UserInterface;

    public function setSender(?UserInterface $sender): self;

    public function getReceiver(): ?UserInterface;

    public function setReceiver(?UserInterface $receiver): self;

    public function getPublishedAt(): ?\DateTimeImmutable;

    public function setPublishedAt(\DateTimeImmutable $publishedAt): self;

    public function getMessage(): ?string;

    public function setMessage(?string $message): self;

    public function getType(): ?string;

    public function setType(?string $type): self;

    public function getStatus(): ?bool;

    public function setStatus(bool $status): self;

    public function getQuantity(): ?int;

    public function setQuantity(?int $quantity): self;

    public function getUser(): ?UserInterface;

    public function setUser(?UserInterface $user): self;
}
