<?php

declare(strict_types=1);

namespace Groshy\Entity;

use Doctrine\ORM\Mapping as ORM;
use Talav\ProfileBundle\Entity\UserRelation as BaseUserRelation;
use Talav\ProfileBundle\Repository\UserRelationRepository;

#[ORM\Entity(repositoryClass: UserRelationRepository::class)]
#[ORM\Table(name: 'user_relation')]
class UserRelation extends BaseUserRelation
{
}
