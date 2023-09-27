<?php

declare(strict_types=1);

namespace Groshy\Entity;

use Doctrine\ORM\Mapping as ORM;
use Talav\ProfileBundle\Entity\UserFriend as BaseUserFriend;
use Talav\ProfileBundle\Repository\UserFriendRepository;

#[ORM\Entity(repositoryClass: UserFriendRepository::class)]
#[ORM\Table(name: 'user_friend')]
class UserFriend extends BaseUserFriend
{
}
