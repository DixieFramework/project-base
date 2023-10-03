<?php

declare(strict_types=1);

namespace Groshy\Entity;

use Doctrine\ORM\Mapping as ORM;
use Talav\PostBundle\Entity\Post as BasePost;
use Talav\PostBundle\Repository\PostRepository;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\Table(name: 'post')]
class Post extends BasePost
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
