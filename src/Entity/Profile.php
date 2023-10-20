<?php

declare(strict_types=1);

namespace Groshy\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Talav\ProfileBundle\Entity\Profile as BaseProfile;

#[ORM\Entity]
#[ORM\Table(name: 'user_profile')]
class Profile extends BaseProfile
{
//	#[ORM\Id]
//	#[ORM\Column(type: 'integer')]
//	#[ORM\GeneratedValue(strategy: 'AUTO')]
//	protected mixed $id;
//    #[ORM\Id]
//    #[ORM\Column(type: 'uuid', unique: true)]
//    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
//    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
//    protected mixed $id = null;
}
