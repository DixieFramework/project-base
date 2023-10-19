<?php

declare(strict_types=1);

namespace Groshy\Entity;

use Doctrine\ORM\Mapping as ORM;
use Talav\ProfileBundle\Entity\FriendshipRequest as BaseFriendshipRequest;
use Talav\ProfileBundle\Repository\FriendshipRequestRepository;

#[ORM\Entity(repositoryClass: FriendshipRequestRepository::class)]
#[ORM\Table(name: 'friendship_request')]
abstract class FriendshipRequest extends BaseFriendshipRequest
{
}
