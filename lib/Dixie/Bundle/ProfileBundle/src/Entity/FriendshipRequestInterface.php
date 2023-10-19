<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Talav\Component\Resource\Model\ResourceInterface;

interface FriendshipRequestInterface extends ResourceInterface
{
    public function getRequester(): ?ProfileInterface;

    public function setRequester(?ProfileInterface $requester): self;

    public function getRequestee(): ?ProfileInterface;

    public function setRequestee(?ProfileInterface $requestee): self;
}
