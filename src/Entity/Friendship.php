<?php

declare(strict_types=1);

namespace Groshy\Entity;

use Doctrine\ORM\Mapping as ORM;
use Talav\ProfileBundle\Entity\Friendship as BaseFriendship;
use Talav\ProfileBundle\Repository\FriendshipRepository;

#[ORM\Entity(repositoryClass: FriendshipRepository::class)]
#[ORM\Table(name: 'friendship')]
class Friendship extends BaseFriendship
{
}
