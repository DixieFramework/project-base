<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Talav\Component\Resource\Model\ResourceInterface;
use Talav\UserBundle\Model\UserInterface;

interface MessageInterface extends ResourceInterface
{
    public function getSender(): ?UserInterface;

    public function setSender(?UserInterface $sender): self;

    public function getReceiver(): ?UserInterface;

    public function setReceiver(?UserInterface $receiver): self;

    public function getContent(): ?string;

    public function setContent(string $content): self;

    public function getSentAt(): ?\DateTimeInterface;

    public function setSentAt(\DateTimeInterface $sentAt): self;

    public function getSeen(): ?bool;

    public function setSeen(bool $seen): self;
}
