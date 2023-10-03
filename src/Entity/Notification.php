<?php

declare(strict_types=1);

namespace Groshy\Entity;

use Doctrine\ORM\Mapping as ORM;
use Talav\ProfileBundle\Entity\Notification as BaseNotification;
use Talav\ProfileBundle\Repository\NotificationRepository;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ORM\Table(name: 'notification')]
class Notification extends BaseNotification
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
