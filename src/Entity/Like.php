<?php

declare(strict_types=1);

namespace Groshy\Entity;

use Doctrine\ORM\Mapping as ORM;
use Talav\ProfileBundle\Entity\Like as BaseLike;
use Talav\ProfileBundle\Repository\LikeRepository;

#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\Table(name: '`like`')]
class Like extends BaseLike
{
	#[ORM\Id]
	#[ORM\Column(type: 'integer')]
	#[ORM\GeneratedValue(strategy: 'AUTO')]
	protected mixed $id;

//    #[Id]
//    #[Column(type: 'uuid', unique: true)]
//    #[GeneratedValue(strategy: 'CUSTOM')]
//    #[CustomIdGenerator(class: UuidGenerator::class)]
//    protected mixed $id = null;
}
