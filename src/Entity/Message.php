<?php

declare(strict_types=1);

namespace Groshy\Entity;

use Doctrine\ORM\Mapping as ORM;
use Talav\ProfileBundle\Entity\Message as BaseMessage;
use Talav\ProfileBundle\Repository\MessageRepository;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: 'message')]
class Message extends BaseMessage
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
