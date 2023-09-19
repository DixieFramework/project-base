<?php

declare(strict_types=1);

namespace Groshy\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Talav\PermissionBundle\Entity\Role as BaseRole;

#[ORM\Entity]
#[ORM\Table(name: 'role')]
class Role extends BaseRole
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected mixed $id = null;

//    #[ORM\Id]
//    #[ORM\Column(type: 'integer')]
//    #[ORM\GeneratedValue(strategy: 'AUTO')]
//    protected mixed $id = null;
}
