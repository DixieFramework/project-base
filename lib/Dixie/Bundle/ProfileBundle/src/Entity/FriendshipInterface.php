<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Talav\Component\Resource\Model\ResourceInterface;

interface FriendshipInterface extends ResourceInterface
{
    public function getProfile(): ?ProfileInterface;

    public function setProfile(?ProfileInterface $user): self;

    public function getFriend(): ?ProfileInterface;

    public function setFriend(?ProfileInterface $friend): self;
}
