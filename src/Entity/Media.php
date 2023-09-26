<?php

declare(strict_types=1);

namespace Groshy\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Talav\MediaBundle\Entity\Media as BaseMedia;

#[ORM\Entity]
#[ORM\Table(name: 'media')]
class Media extends BaseMedia
{
//    #[ORM\Id]
//    #[ORM\Column(type: 'integer', unique: true)]
//    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected mixed $id = null;
}
